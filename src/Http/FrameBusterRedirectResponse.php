<?php

namespace CodeCloud\Bundle\ShopifyBundle\Http;

use Symfony\Component\HttpFoundation\Response;

class FrameBusterRedirectResponse extends Response
{
    public function __construct(string $url)
    {
        parent::__construct('<script>
    window.top.location.href = "'.$url.'";
</script>
', Response::HTTP_OK, ['Content-Type', 'text/html']);
    }
}
