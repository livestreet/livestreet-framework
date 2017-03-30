<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet type="text/xsl" href="{$sTemplateDirWeb}sitemap.xsl"?>
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"
        xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    {foreach from=$aData item=oItem}
        <url>
            <loc>{$oItem.loc}</loc>
            {if $oItem.lastmod}
                <lastmod>{$oItem.lastmod}</lastmod>
            {/if}
            {if $oItem.changefreq}
                <changefreq>{$oItem.changefreq}</changefreq>
            {/if}
            {if $oItem.priority}
                <priority>{$oItem.priority}</priority>
            {/if}
        </url>
    {/foreach}
</urlset>