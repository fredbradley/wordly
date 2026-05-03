<?php

namespace Database\Seeders;

use App\Models\Word;
use Illuminate\Database\Seeder;

class DailyWordSeeder extends Seeder
{
    public function run(): void
    {
        $words = [
            'crane', 'slate', 'trace', 'plumb', 'grove', 'flint', 'scout', 'blaze', 'shone', 'crisp',
            'trove', 'glide', 'perch', 'cleft', 'smirk', 'brash', 'dwelt', 'fjord', 'gripe', 'lofty',
            'pleat', 'swoop', 'murky', 'blunt', 'chide', 'clout', 'frown', 'grasp', 'brine',
            'quirk', 'stomp', 'vexed', 'whisk', 'zesty', 'abode', 'bland', 'clamp', 'depot', 'expel',
            'flair', 'gloom', 'harsh', 'infer', 'joust', 'kneel', 'lusty', 'marsh', 'notch', 'octet',
            'prawn', 'quell', 'realm', 'snout', 'tryst', 'ulcer', 'vapid', 'wrath', 'yacht', 'yearn',
            'abhor', 'brood', 'champ', 'drool', 'exile', 'fetid', 'guile', 'havoc', 'icing', 'jaded',
            'knack', 'lunge', 'maxim', 'optic', 'query', 'rivet', 'snare', 'tawny',
            'undue', 'valor', 'extol', 'yodel', 'zoned', 'agile', 'birch', 'caulk', 'deter',
            'evoke', 'fudge', 'gauze', 'haste', 'impel', 'jaunt', 'koala', 'lilac', 'mirth', 'nymph',
            'onset', 'prowl', 'quaff', 'remit', 'shawl', 'thyme', 'untie', 'vouch', 'witty',
            'abbey', 'brisk', 'clasp', 'ensue', 'fleck', 'grimy', 'hoist', 'inlet', 'jiffy',
            'knobs', 'leapt', 'moist', 'noisy', 'outdo', 'plaid', 'qualm', 'rouse', 'sinew', 'tithe',
            'usurp', 'verge', 'windy', 'afoot', 'bevel', 'crimp', 'duchy', 'emote', 'flout', 'gusto',
            'hound', 'irked', 'kitty', 'lithe', 'nexus', 'occur', 'pixel', 'quota',
            'repel', 'spire', 'taint', 'unwed', 'vicar', 'woven', 'adorn', 'churn',
            'elude', 'filth', 'groan', 'humid', 'joist', 'lodge', 'medal', 'nudge',
            'ovoid', 'preen', 'quilt', 'repay', 'shrub', 'tapir', 'umbra', 'vigor', 'wince',
            'booth', 'chasm', 'dirge', 'floss', 'graft', 'heron', 'irony', 'knoll',
            'mauve', 'niece', 'offer', 'prism', 'relic', 'shred', 'taunt', 'ultra',
            'venom', 'abbot', 'bliss', 'cello', 'dross', 'egret', 'frond', 'glyph', 'hippo',
            'ingot', 'jumbo', 'knelt', 'latch', 'macro', 'niche', 'ozone', 'padre', 'quake', 'rebus',
            'scald', 'tempt', 'waltz', 'axiom', 'braid', 'creep', 'delta', 'envoy',
            'ficus', 'glare', 'helix', 'icier', 'junta', 'kebab', 'lefty', 'metro', 'nutty', 'ovule',
            'piano', 'ripen', 'squab', 'tibia', 'vinyl', 'annex',
            'comet', 'edify', 'grime', 'hazel', 'idiom', 'leach',
            'mango', 'parka', 'quest', 'robin', 'scone', 'umber',
            'burly', 'chirp', 'disco', 'erupt', 'freak', 'gruel', 'haunt',
            'inane', 'jumpy', 'kayak', 'lapel', 'maple', 'ovary', 'pupil', 'radon',
            'scrum', 'taboo', 'unzip', 'vivid', 'wring', 'belle', 'crumb', 'debut', 'erode',
            'flume', 'gavel', 'hyena', 'icily', 'jewel', 'kiosk', 'lance', 'melee', 'oxide',
            'plier', 'rabbi', 'savor', 'tabby', 'whelp', 'yawns', 'abash',
            'bleat', 'crawl', 'datum', 'enact', 'finch', 'grind', 'homer', 'inter', 'jazzy', 'klutz',
            'libel', 'mocha', 'nadir', 'oaken', 'pinch', 'quash', 'rigor', 'scrub', 'talon', 'usher',
            'vaunt', 'wizen', 'adage', 'boxer', 'flank', 'gruff', 'hello',
            'kudos', 'lemon', 'manor', 'plaza',
            'sniff', 'tonal', 'value', 'wider', 'abide', 'bison', 'dance', 'event',
            'feast', 'greet', 'happy', 'learn', 'north', 'place', 'queen', 'round', 'slope',
            'tower', 'union', 'youth', 'aster', 'bloom', 'cheer', 'drift', 'ember', 'frank',
            'plant', 'stone', 'light', 'music', 'world', 'party', 'heart', 'money', 'power',
            'green', 'black', 'white', 'brown', 'sunny', 'rainy', 'storm', 'cloud', 'river',
            'ocean', 'table', 'chair', 'piano', 'chess', 'brush', 'sword', 'shield', 'arrow',
            'flame', 'smoke', 'earth', 'water', 'bread', 'olive', 'sugar', 'lemon', 'peach',
            'grape', 'plumb', 'melon', 'cedar', 'maple', 'birch', 'coral', 'amber', 'ivory',
        ];

        $words = array_unique(array_filter($words, fn ($w) => strlen($w) === 5 && ctype_alpha($w)));

        foreach ($words as $word) {
            Word::firstOrCreate(['word' => strtolower($word)]);
        }

        $this->command?->info(count($words).' words loaded into the word bank.');
    }
}
