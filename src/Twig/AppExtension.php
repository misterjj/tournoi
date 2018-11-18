<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    const CHARACTER_NAME = [
        'bayonetta' => 'Bayonetta',
        'bowser' => 'Bowser',
        'bowser_jr' => 'Bowser Jr.',
        'captain_falcon' => 'Captain Falcon',
        'cloud' => 'Cloud',
        'corrin' => 'Corrin',
        'daisy' => 'Daisy',
        'dark_pit' => 'Dark Pit',
        'diddy_kong' => 'Diddy Kong',
        'donkey_kong' => 'Donkey Kong',
        'dr_mario' => 'Dr. Mario',
        'duck_hunt' => 'Duck Hunt',
        'falco' => 'Falco',
        'fox' => 'Fox',
        'ganondorf' => 'Ganondorf',
        'greninja' => 'Greninja',
        'ice_climbers' => 'Ice Climbers',
        'ike' => 'Ike',
        'inkling' => 'Inkling',
        'jigglypuff' => 'Jigglypuff',
        'king_dedede' => 'King Dedede',
        'kirby' => 'Kirby',
        'link' => 'Link',
        'little_mac' => 'Little Mac',
        'lucario' => 'Lucario',
        'lucas' => 'Lucas',
        'lucina' => 'Lucina',
        'luigi' => 'Luigi',
        'mario' => 'Mario',
        'marth' => 'Marth',
        'mega_man' => 'Mega Man',
        'meta_knight' => 'Meta Knight',
        'mewtwo' => 'Mewtwo',
        'mii' => 'Mii',
        'mr_game_watch' => 'Mr. Game & Watch',
        'ness' => 'Ness',
        'olimar' => 'Olimar',
        'pac_man' => 'PAC-MAN',
        'palutena' => 'Palutena',
        'peach' => 'Peach',
        'pichu' => 'Pichu',
        'pikachu' => 'Pikachu',
        'pit' => 'Pit',
        'pokemon_trainer' => 'Pokemon Trainer',
        'rob' => 'R.O.B.',
        'ridley' => 'Ridley',
        'robin' => 'Robin',
        'rosalina_luma' => 'Rosalina & Luma',
        'roy' => 'Roy',
        'ryu' => 'Ryu',
        'samus' => 'Samus',
        'sheik' => 'Sheik',
        'shulk' => 'Shulk',
        'snake' => 'Snake',
        'sonic' => 'Sonic',
        'toon_link' => 'Toon Link',
        'villager' => 'Villager',
        'wario' => 'Wario',
        'wii_fit_trainer' => 'Wii Fit Trainer',
        'wolf' => 'Wolf',
        'yoshi' => 'Yoshi',
        'young_link' => 'Young Link',
        'zelda' => 'Zelda',
        'zero_suit_samus' => 'Zero Suit Samus',
    ];

    public function getFunctions()
    {
        return array(
            new TwigFunction('realname', array($this, 'realnameDisplay')),
        );
    }

    public function realnameDisplay(string $name)
    {

        return isset(self::CHARACTER_NAME[$name]) ? self::CHARACTER_NAME[$name] : $name;
    }
}