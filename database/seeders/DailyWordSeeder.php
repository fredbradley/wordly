<?php

namespace Database\Seeders;

use App\Models\DailyWord;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DailyWordSeeder extends Seeder
{
    public function run(): void
    {
        $words = [
            'crane','slate','trace','plumb','grove','flint','scout','blaze','shone','crisp',
            'trove','glide','perch','cleft','smirk','brash','dwelt','fjord','gripe','lofty',
            'pleat','knave','swoop','murky','blunt','chide','clout','frown','grasp','brine',
            'quirk','stomp','vexed','whisk','zesty','abode','bland','clamp','depot','expel',
            'flair','gloom','harsh','infer','joust','kneel','lusty','marsh','notch','octet',
            'prawn','quell','realm','snout','tryst','ulcer','vapid','wrath','yacht','yearn',
            'abhor','brood','champ','drool','exile','fetid','guile','havoc','icing','jaded',
            'knack','lunge','maxim','nitty','optic','plumb','query','rivet','snare','tawny',
            'undue','valor','welts','extol','yodel','zoned','agile','birch','caulk','deter',
            'evoke','fudge','gauze','haste','impel','jaunt','koala','lilac','mirth','nymph',
            'onset','prowl','quaff','remit','shawl','thyme','untie','vouch','witty','expunge',
            'abbey','brisk','clasp','doing','ensue','fleck','grimy','hoist','inlet','jiffy',
            'knobs','leapt','moist','noisy','outdo','plaid','qualm','rouse','sinew','tithe',
            'usurp','verge','windy','afoot','bevel','crimp','duchy','emote','flout','gusto',
            'hound','irked','jaunt','kitty','lithe','moult','nexus','occur','pixel','quota',
            'repel','spire','taint','unwed','vicar','woven','adorn','bolts','churn','depot',
            'elude','filth','groan','humid','impel','joist','knave','lodge','medal','nudge',
            'ovoid','preen','quilt','repay','shrub','tapir','umbra','vigor','wince','axles',
            'booth','chasm','dirge','eclat','floss','graft','heron','irony','joust','knoll',
            'lusty','mauve','niece','offer','prism','quota','relic','shred','taunt','ultra',
            'venom','wrath','abbot','bliss','cello','dross','egret','frond','glyph','hippo',
            'ingot','jumbo','knelt','latch','macro','niche','ozone','padre','quake','rebus',
            'scald','tempt','unfed','voila','waltz','axiom','braid','creep','delta','envoy',
            'ficus','glare','helix','icier','junta','kebab','lefty','metro','nutty','ovule',
            'piano','quaff','ripen','squab','tibia','untie','vinyl','wobble','annex','blaze',
            'comet','duple','edify','flair','grime','hazel','idiom','jokey','kanji','leach',
            'mango','nitwit','offal','parka','quest','robin','scone','tubby','umber','vetch',
            'waltz','yeast','abode','burly','chirp','disco','erupt','freak','gruel','haunt',
            'inane','jumpy','kayak','lapel','maple','nooks','ovary','pupil','quaff','radon',
            'scrum','taboo','unzip','vivid','wring','abbey','belle','crumb','debut','erode',
            'flume','gavel','hyena','icily','jewel','kiosk','lance','melee','nugget','oxide',
            'plier','quirk','rabbi','savor','tabby','ulnar','voila','whelp','yawns','abash',
            'bleat','crawl','datum','enact','finch','grind','homer','inter','jazzy','klutz',
            'libel','mocha','nadir','oaken','pinch','quash','rigor','scrub','talon','usher',
            'vaunt','wizen','adage','boxer','crisp','dirge','edema','flank','gruff','hello',
            'impel','jinxy','kudos','lemon','manor','niece','onset','plaza','quell','repay',
            'sniff','tonal','ultra','value','wider','abide','bison','could','dance','event',
            'feast','greet','happy','learn','modal','north','place','queen','round','slope',
            'tower','union','vivid','youth','aster','bloom','cheer','drift','ember','frank',
        ];

        $words = array_unique($words);
        shuffle($words);

        $start = Carbon::create(2026, 1, 1);
        foreach ($words as $i => $word) {
            $date = $start->copy()->addDays($i)->toDateString();
            DailyWord::updateOrCreate(['date' => $date], ['word' => strtolower($word)]);
        }
    }
}
