<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:fo="http://www.w3.org/1999/XSL/Format">

    <xsl:template match="/">
        <fo:root>
            <fo:layout-master-set>
                <fo:simple-page-master master-name="A4">
                    <fo:region-body margin="1in"/>
                </fo:simple-page-master>
            </fo:layout-master-set>

            <fo:page-sequence master-reference="A4">
                <fo:flow flow-name="xsl-region-body">
                    <fo:block>
                        <fo:block font-size="24pt" font-weight="bold" text-align="center" space-after="12pt">
                            <xsl:value-of select="/ville/@nom"/>
                        </fo:block>
                        <fo:block font-size="12pt" text-align="justify" space-after="12pt">
                            <xsl:value-of select="/ville/descriptif"/>
                        </fo:block>

                        <fo:block font-size="18pt" font-weight="bold" space-before="12pt">Sites Touristiques</fo:block>
                        <fo:list-block>
                            <xsl:for-each select="/ville/sites/site">
                                <fo:list-item>
                                    <fo:list-item-label end-indent="label-end()">
                                        <fo:block>-</fo:block>
                                    </fo:list-item-label>
                                    <fo:list-item-body start-indent="body-start()">
                                        <fo:block><xsl:value-of select="@nom"/></fo:block>
                                    </fo:list-item-body>
                                </fo:list-item>
                            </xsl:for-each>
                        </fo:list-block>

                        <fo:block font-size="18pt" font-weight="bold" space-before="12pt">Hôtels</fo:block>
                        <fo:list-block>
                            <xsl:for-each select="/ville/hotels/hotel">
                                <fo:list-item>
                                    <fo:list-item-label end-indent="label-end()">
                                        <fo:block>-</fo:block>
                                    </fo:list-item-label>
                                    <fo:list-item-body start-indent="body-start()">
                                        <fo:block><xsl:value-of select="."/></fo:block>
                                    </fo:list-item-body>
                                </fo:list-item>
                            </xsl:for-each>
                        </fo:list-block>

                        <fo:block font-size="18pt" font-weight="bold" space-before="12pt">Restaurants</fo:block>
                        <fo:list-block>
                            <xsl:for-each select="/ville/restaurants/restaurant">
                                <fo:list-item>
                                    <fo:list-item-label end-indent="label-end()">
                                        <fo:block>-</fo:block>
                                    </fo:list-item-label>
                                    <fo:list-item-body start-indent="body-start()">
                                        <fo:block><xsl:value-of select="."/></fo:block>
                                    </fo:list-item-body>
                                </fo:list-item>
                            </xsl:for-each>
                        </fo:list-block>

                        <fo:block font-size="18pt" font-weight="bold" space-before="12pt">Gares</fo:block>
                        <fo:list-block>
                            <xsl:for-each select="/ville/gares/gare">
                                <fo:list-item>
                                    <fo:list-item-label end-indent="label-end()">
                                        <fo:block>-</fo:block>
                                    </fo:list-item-label>
                                    <fo:list-item-body start-indent="body-start()">
                                        <fo:block><xsl:value-of select="."/></fo:block>
                                    </fo:list-item-body>
                                </fo:list-item>
                            </xsl:for-each>
                        </fo:list-block>

                        <fo:block font-size="18pt" font-weight="bold" space-before="12pt">Aéroports</fo:block>
                        <fo:list-block>
                            <xsl:for-each select="/ville/aéroports/aéroport">
                                <fo:list-item>
                                    <fo:list-item-label end-indent="label-end()">
                                        <fo:block>-</fo:block>
                                    </fo:list-item-label>
                                    <fo:list-item-body start-indent="body-start()">
                                        <fo:block><xsl:value-of select="."/></fo:block>
                                    </fo:list-item-body>
                                </fo:list-item>
                            </xsl:for-each>
                        </fo:list-block>
                    </fo:block>
                </fo:flow>
            </fo:page-sequence>
        </fo:root>
    </xsl:template>
</xsl:stylesheet>
