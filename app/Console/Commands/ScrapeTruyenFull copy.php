<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Weidner\Goutte\GoutteFacade;

class ScrapeTruyenFull extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:fullTruyen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $crawler = GoutteFacade::request('GET', 'https://truyenfull.vn/danh-sach/truyen-full/trang-630/');
        $title = $crawler->filter('h3.truyen-title>a')->each(function ($node) {
            return $node->attr("href");
        });

        // $content = $crawler->filter('.chapter-c')->each(function ($node) {
        //     return $node->text();
        // })[0];

        foreach ($title as $link) {
            print($link . "\n");
        }
    }
}
