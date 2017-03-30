<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:html="http://www.w3.org/TR/REC-html40" xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes"/>
    <xsl:template match="/">
        <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <title>XML Sitemap Index</title>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <style type="text/css">
                    body {
                    font-family: Verdana, Arial, Helvetica, sans-serif;
                    font-size: 12px;
                    }

                    #header, #footer {
                    background-color: #F0F7FC;
                    border: 1px #50A6E0 solid;
                    border-right: none; border-left: none;
                    padding: 5px 10px;
                    margin: 10px 0px;
                    line-height: 1.7;
                    }

                    #header a, #footer a {
                    color: #2266bb;
                    }

                    #footer {
                    margin-top: 15px;
                    }

                    table {
                    width: 100%;
                    }

                    th {
                    text-align: left;
                    border-bottom: 1px solid #aaaaaa;
                    padding-bottom: 10px; padding-left: 5px;
                    }

                    tr.odd {
                    background-color: #f7f7f7;
                    }

                    td {
                    padding: 5px;
                    }
                </style>
            </head>
            <body>
                <h1>XML Sitemap Index</h1>
                <div id="intro">
                    <p>
                        This is a XML Sitemap Index which is supposed to be processed by search engines like <a href="http://www.google.com">Google</a>, <a href="http://search.msn.com">MSN Search</a> and <a href="http://www.yahoo.com">YAHOO</a>.<br />
                        You can find more information about XML sitemaps on <a href="http://sitemaps.org">sitemaps.org</a> and Google's <a href="http://code.google.com/sm_thirdparty.html">list of sitemap programs</a>.
                    </p>
                </div>
                <div id="content">
                    <table cellpadding="0" cellspacing="0">
                        <tr>
                            <th style="width: 70%;">Location</th>
                            <th>Last Modified</th>
                        </tr>
                        <xsl:for-each select="sitemap:sitemapindex/sitemap:sitemap">
                            <tr>
                                <xsl:if test="position() mod 2 != 0">
                                    <xsl:attribute name="class">odd</xsl:attribute>
                                </xsl:if>
                                <td>
                                    <xsl:variable name="itemURL">
                                        <xsl:value-of select="sitemap:loc"/>
                                    </xsl:variable>
                                    <a href="{$itemURL}">
                                        <xsl:value-of select="sitemap:loc"/>
                                    </a>
                                </td>
                                <td>
                                    <xsl:value-of select="concat(substring(sitemap:lastmod, 0, 11),concat(' ', substring(sitemap:lastmod, 12, 5)))"/>
                                </td>
                            </tr>
                        </xsl:for-each>
                    </table>
                </div>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>