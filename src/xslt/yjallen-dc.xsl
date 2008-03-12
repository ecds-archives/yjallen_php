<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
		xmlns:dc="http://purl.org/dc/elements/1.1/"
		xmlns:dcterms="http://purl.org/dc/terms"
                version="1.0">
  <!-- This stylesheet creates Dublin core metadata for each document -->
  <xsl:output method="xml" omit-xml-declaration="yes"/>
  <xsl:variable name="baseurl">http://beck.library.emory.edu/</xsl:variable>
  <xsl:variable name="siteurl">YoungJohnAllen</xsl:variable>

  <xsl:template match="/">
    <dc>
      <xsl:apply-templates select="//TEI"/> <!-- get everything below this -->
      
      <dc:type>Text</dc:type>
    <dc:format>text/xml</dc:format>
    </dc>
  </xsl:template>

  
  
  <xsl:template match="extent"/>
  
  <xsl:template match="fileDesc">
    <xsl:element name="dc:title">
      <xsl:apply-templates select="titleStmt/title"/>
    </xsl:element>
    <xsl:element name="dc:creator">
      <xsl:value-of select="titleStmt//name//reg"/>
    </xsl:element>
        <xsl:element name="dc:contributor">
      <xsl:text>Lewis H. Beck Center</xsl:text>
    </xsl:element>
    <xsl:element name="dc:publisher">
      <xsl:apply-templates select="publicationStmt/publisher"/>
    </xsl:element>
    <xsl:element name="dcterms:issued">
      <xsl:apply-templates select="publicationStmt/date"/>
    </xsl:element>
    <xsl:element name="dc:rights">
      <xsl:apply-templates select="publicationStmt/availability/p"/>
    </xsl:element>
    
    <xsl:element name="dcterms:isPartOf">
      <xsl:apply-templates select="seriesStmt/title"/>
    </xsl:element>
    <xsl:element name="dcterms:isPartOf">
      <xsl:value-of select="$baseurl"/><xsl:value-of
        select="$siteurl"/>      
    </xsl:element>
    
    <xsl:element name="dc:source">
      <!-- only one element here. -->
      <xsl:apply-templates select="sourceDesc/bibl"/>
      <!-- in case source is in plain text, without tags -->
      <!--  <xsl:apply-templates select="text()"/> -->
    </xsl:element>
    
    <xsl:element name="dc:identifier">
      <xsl:value-of select="publicationStmt/idno[@type='ark']"/>      
    </xsl:element>     
  </xsl:template>
  
  <xsl:template match="publicationStmt"> 
    <!-- electronic publication date: Per advice of LA -->
    <xsl:element name="dcterms:created">
      <xsl:value-of select="date"/>
    </xsl:element>
  </xsl:template>
  

<!-- normalize space in titles -->
  <xsl:template match="titleStmt/title">
    <xsl:value-of select="normalize-space(.)"/>
  </xsl:template>

<!-- add a space after titles in the head -->
  <xsl:template match="titleStmt/title">
    <xsl:apply-templates/><xsl:text> </xsl:text>
  </xsl:template>

<!-- handle <lb/> in head -->
   <xsl:template match="lb">
      <xsl:apply-templates/><xsl:text> </xsl:text>
   </xsl:template>


  <!-- ignore these: encoding specific information -->
  <xsl:template match="encodingDesc"/>
  <xsl:template match="encodingDesc/projectDesc"/>
  <xsl:template match="encodingDesc/tagsDecl"/>
  <xsl:template match="encodingDesc/refsDecl"/>
  <xsl:template match="encodingDesc/editorialDecl"/>
  <xsl:template match="profileDesc/creation"/>
  <xsl:template match="revisionDesc"/>
  <xsl:template match="text"/>

  <!-- normalize space for all text nodes -->
  <xsl:template match="text()">
    <xsl:value-of select="normalize-space(.)"/>
  </xsl:template>

</xsl:stylesheet>
