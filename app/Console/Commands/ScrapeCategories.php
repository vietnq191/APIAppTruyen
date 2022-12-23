<?php

namespace App\Console\Commands;

use App\Models\Categories;
use Illuminate\Console\Command;
use Weidner\Goutte\GoutteFacade;

class ScrapeCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:categories';

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
        $crawler = GoutteFacade::request('GET', 'https://truyenfull.vn/');
        $links = $crawler->filter('div.list-cat>.row>.col-xs-6>a')->each(function ($node) {
            return $node->attr("href");
        });

        foreach ($links as $link) {
            $crawler_detail = GoutteFacade::request('GET', $link);
            $title = $crawler_detail->filter('.list-truyen>.title-list>h2')->each(function ($node) {
                return $node->text();
            });

            $descriptions = $crawler_detail->filter('.cat-desc>.panel-body')->each(function ($node) {
                return $node->html();
            });

            foreach ($title as $name) {
                if($name == "Truyện Teen")
                {
                    $name = $name;
                }
                else
                {
                    $name = str_replace('Truyện ', '', $name);
                }
            }

            foreach ($descriptions as $description) {
                $description = $description;
            }

            print($name . "\n");

            Categories::updateOrCreate(
                [
                    'category_name' => $name,
                ],
                [
                    'description' => $description,
                ]
            );
        }
    }
}
