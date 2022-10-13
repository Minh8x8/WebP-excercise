<?php
namespace App\Twig;

use http\Env\Request;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\TwigFilter;

class MyExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('selectedCat', [$this, 'selectedCatTwig']),
        ];
    }

    public function selectedCatTwig(Request $request): int
    {
        return $request->get('cat');
    }

}
?>