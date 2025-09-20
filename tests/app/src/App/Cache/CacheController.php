<?php


use Stormmore\Framework\Mvc\Attributes\Controller;
use Stormmore\Framework\Mvc\Attributes\Post;
use Stormmore\Framework\Mvc\Attributes\Route;
use Stormmore\Framework\Mvc\IO\Redirect;
use Stormmore\Framework\Mvc\IO\Request;
use Stormmore\Framework\Mvc\IO\Response;
use Stormmore\Framework\Mvc\View\View;

#[Controller]
readonly class CacheController
{
    public function __construct(private Request $request, private Response $response)
    {
    }

    #[Route("/cache")]
    public function index(): View
    {
        $id = $this->request->query->get("id", false);
        $key = $id ? "/cache?id=$id" : "/cache";
        $this->response->addHeader("x-cache-request", $key);
        $this->response->addHeader('x-custom-header', "3");
        return view("cache/index", [
            "id" => $id,
            'timestamp' => (new DateTime())->format("Y-m-d H:i:s.v"),
        ]);
    }

    #[Post]
    #[Route("/cache/remove")]
    public function remove(?int $id = null): Redirect
    {
        $url = $id === null ? "/cache" : "/cache?id=$id";
        $this->response->addHeader("x-cache-delete", $url);
        return redirect($url);
    }

    #[Post]
    #[Route("/cache/remove-all")]
    public function removeAll(?int $id = null): Redirect
    {
        $url = $id == null ? "/cache" : "/cache?id=$id";
        $this->response->addHeader("x-cache-delete-like", "/cache");
        return redirect($url);
    }
}