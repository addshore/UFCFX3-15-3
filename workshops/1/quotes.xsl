<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" encoding="utf-8" indent="yes"/>
    <xsl:template match="/">
        <xsl:text disable-output-escaping="yes">&lt;!DOCTYPE html>&#10;</xsl:text>
        <html>
            <head>
                <title>Quotes XML -> HTML using XSLT</title>
            </head>
            <body>
                <h2>Quote Collection</h2>
                <p>Please see quotes below.</p>
                <table border="1">
                    <tr bgcolor="#9acd32">
                        <th style="text-align:left">Category</th>
                        <th style="text-align:left">Quote</th>
                        <th style="text-align:left">Author</th>
                        <th style="text-align:left">Birth and Death</th>
                        <th style="text-align:left">Image</th>
                    </tr>
                    <xsl:for-each select="quotes/quote">
                        <tr>
                            <td><xsl:value-of select="category"/></td>
                            <td><xsl:value-of select="text"/></td>
                            <td>
                                <xsl:element name="a">
                                    <xsl:attribute name="href">
                                        <xsl:value-of select="wplink"/>
                                    </xsl:attribute>
                                    <xsl:value-of select="source"/>
                                </xsl:element>
                            </td>
                            <td><xsl:value-of select="dob-dod"/></td>
                            <td>
                            <xsl:element name="img">
                                <xsl:attribute name="src">
                                    <xsl:value-of select="wpimg"/>
                                </xsl:attribute>
                                <xsl:attribute name="width">110px</xsl:attribute>
                                <xsl:attribute name="height">110px</xsl:attribute>
                            </xsl:element>
                            </td>
                        </tr>
                    </xsl:for-each>
                </table>
                <p>This is some footer!</p>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>