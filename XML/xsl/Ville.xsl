<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <!-- Déclaration de sortie en HTML -->
    <xsl:output method="html" encoding="UTF-8" indent="yes"/>

    <!-- Template principal -->
    <xsl:template match="/">
        <html>
        <head>
            <title><xsl:value-of select="/ville/@nom"/></title>
            <link rel="stylesheet" type="text/css" href="styles/pageville.css"/>
        </head>
        <body>
            <h1><xsl:value-of select="/ville/@nom"/></h1>
            <p><xsl:value-of select="/ville/descriptif"/></p>

            <h2>Sites Touristiques</h2>
            <ul>
                <xsl:for-each select="/ville/sites/site">
                    <li>
                        <img src="{@photo}" alt="{@nom}"/>
                        <xsl:value-of select="@nom"/>
                    </li>
                </xsl:for-each>
            </ul>

            <h2>Hôtels</h2>
            <ul>
                <xsl:for-each select="/ville/hotels/hotel">
                    <li><xsl:value-of select="."/></li>
                </xsl:for-each>
            </ul>

            <h2>Restaurants</h2>
            <ul>
                <xsl:for-each select="/ville/restaurants/restaurant">
                    <li><xsl:value-of select="."/></li>
                </xsl:for-each>
            </ul>

            <h2>Gares</h2>
            <ul>
                <xsl:for-each select="/ville/gares/gare">
                    <li><xsl:value-of select="."/></li>
                </xsl:for-each>
            </ul>

            <h2>Aéroports</h2>
            <ul>
                <xsl:for-each select="/ville/aéroports/aéroport">
                    <li><xsl:value-of select="."/></li>
                </xsl:for-each>
            </ul>

            <button onclick="generatePDF(event)">Transformer en PDF</button>
        </body>
        </html>
    </xsl:template>
</xsl:stylesheet>
