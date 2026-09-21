<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class SliderArtistCard
{
    public string $name;
    public string $images;
    public string $profileImage;
    public string $whatsapp;
    public string $instagram;
    public string $facebook;
}
 