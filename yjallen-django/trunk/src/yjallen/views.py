import os
from urllib import urlencode

from django.conf import settings
from django.shortcuts import render_to_response
from django.http import HttpResponse, Http404
from django.core.paginator import Paginator, InvalidPage, EmptyPage
from django.template import RequestContext

from yjallen.models import LetterTitle, Letter, Bibliography, SourceDescription, LetterSearch
from yjallen.forms import LetterSearchForm

from eulcommon.djangoextras.http.decorators import content_negotiation
from eulexistdb.query import escape_string
from eulexistdb.exceptions import DoesNotExist # ReturnedMultiple needed also ?
 
def letters(request):
  letters = LetterTitle.objects.only('id', 'title', 'date').order_by('date')
  return render_to_response('letters.html', {'letters' : letters})
  context_instance=RequestContext(request)

def letter_display(request, doc_id):
    "Display the contents of a single letter."
    try:
        letter = LetterTitle.objects.get(id__exact=doc_id)
        format = letter.xsl_transform(filename=os.path.join(settings.BASE_DIR, 'xslt', 'form.xsl'))
        return render_to_response('letter_display.html', {'letter': letter, 'format': format.serialize()}, context_instance=RequestContext(request))
    except DoesNotExist:
        raise Http404

def searchbox(request):
    "Search letters by title/author/keyword"
    form = LetterSearchForm(request.GET)
    response_code = None
    search_opts = {}
    all_letters = None
    number_of_results = 10
   
   
    if form.is_valid():
        #if 'title' in form.cleaned_data and form.cleaned_data['title']:
            #search_opts['title_list__fulltext_terms'] = '%s' % form.cleaned_data['title']
        if 'author' in form.cleaned_data and form.cleaned_data['author']:
            search_opts['Letter.letter_author__fulltext_terms'] = '%s' % form.cleaned_data['author']
        #if 'keyword' in form.cleaned_data and form.cleaned_data['keyword']:
            #search_opts['fulltext_terms'] = '%s' % form.cleaned_data['keyword']

        #letters = LetterSearch.objects.only(id__exact=doc_id).filter(**search_opts)
        letters = Letter.objects.only("letter__title","letter__id","title", "id").filter(**search_opts)
        #if 'keyword' in form.cleaned_data and form.cleaned_data['keyword']:
            #letters = letters.only_raw(line_matches='%%(xq_var)s//tei:l[ft:query(., "%s")]' \
                                    #% escape_string(form.cleaned_data['keyword']))
        all_letters = letters.all()
        searchbox_paginator = Paginator(all_letters, number_of_results)
        try:
            page = int(request.GET.get('page', '1'))
        except ValueError:
            page = 1
        # If page request (9999) is out of range, deliver last page of results.
        try:
            searchbox_page = searchbox_paginator.page(page)
        except (EmptyPage, InvalidPage):
            searchbox_page = searchbox_paginator.page(paginator.num_pages)
           
        response = render_to_response('search.html', {
                "searchbox": form,
                "all_letters_paginated": searchbox_page,
                #"keyword": form.cleaned_data['keyword'],
                #"title": form.cleaned_data['title'],
                "author": form.cleaned_data['author'],
        },
        context_instance=RequestContext(request))
    #no search conducted yet, default form
    else:
        response = render_to_response('search.html', {
                    "searchbox": form
            },
            context_instance=RequestContext(request))
       
    if response_code is not None:
        response.status_code = response_code
    return response  
