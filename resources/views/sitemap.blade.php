@php '<?xml version="1.0" encoding="UTF-8"?>'; @endphp
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>https://collection-lvivgallery.org.ua/uk</loc>
        <changefreq>monthly</changefreq>
        <priority>1</priority>
    </url>
    <url>
        <loc>https://collection-lvivgallery.org.ua/en</loc>
        <changefreq>monthly</changefreq>
        <priority>1</priority>
    </url>
    @foreach ($objects as $object)
        <url>
            <loc>https://collection-lvivgallery.org.ua/{{$object['lang']}}/object/{{$object['id']}}-{{$object['url']}}</loc>
            <changefreq>monthly</changefreq>
            <priority>0.7</priority>
        </url>
    @endforeach
</urlset>