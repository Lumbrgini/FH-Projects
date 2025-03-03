<?php

namespace App\Application\Actions;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;

class RandomQuoteAction extends Action
{
    private Twig $twig;

    public function __construct(LoggerInterface $logger, Twig $twig)
    {
        parent::__construct($logger);
        $this->twig = $twig;
    }

    protected function action(): Response
    {
        // Create a Guzzle client
        $client = new Client();

        // Send a GET request to the ZenQuotes API
        $responseZEN = $client->request('GET', 'https://zenquotes.io/api/random');
        $quoteData = json_decode($responseZEN->getBody()->getContents(), true);

        // Assuming the response structure is an array with one object containing the quote
        $quote = $quoteData[0]['q'] ?? 'No quote found';
        $author = $quoteData[0]['a'] ?? 'Unknown';

        // Initialize variables for author summary and image URL
        $authorSummary = 'No additional information available';
        $orgimg = 'No image available';

        if ($author !== 'Unknown') {

            // Send a GET request to the Wikipedia API for the author summary and image
            $responseWiki = $client->request('GET', 'https://en.wikipedia.org/api/rest_v1/page/summary/' . $author);
            $wikiData = json_decode($responseWiki->getBody()->getContents(), true);

            if (isset($wikiData['extract'])) {
                $authorSummary = $wikiData['extract'];
            }

            if (isset($wikiData['originalimage']['source'])) {
                $orgimg = $wikiData['originalimage']['source'];
            }
        }

        // Render the response using Twig
        return $this->twig->render($this->response, 'quote.html.twig', [
            'quote' => $quote,
            'author' => $author,
            'authorSummary' => $authorSummary,
            'originalimage' => $orgimg
        ]);

        // 1. Zen quotes api abfragen
        // 2. wikipedia api abfragen
        // 3. Twig-template rendern und informationen einfÃ¼gen
        // Fehlerbehandlung nicht vergessen
    }
}