<?php

namespace App\Console\Commands;

use App\Models\Chapter;
use App\Models\Story;
use App\Models\StoryCategories;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Weidner\Goutte\GoutteFacade;
use Illuminate\Support\Str;

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
        //Last number paginate of https://truyenfull.vn/danh-sach/truyen-full
        $lastPage = 650;
        // Xong 47 trang rồi
        for ($number = 1; $number <= 5; $number++) {
            printf("Page " . $number . "\n");
            $crawler = GoutteFacade::request('GET', 'https://truyenfull.vn/danh-sach/truyen-moi/trang-' . $number);
            $links = $crawler->filter('h3.truyen-title>a')->each(function ($node) {
                return $node->attr("href");
            });

            //Scrape stories has full chapter
            //Status = 1 is meaning Full
            $status = 1;

            //Map all links story
            foreach ($links as $link) {
                $this->saveStory($link);
            }
            // break;
        }
    }

    private function saveStory($link)
    {
        //Prepare variable
        $title = "";
        $description = "";
        $author = "";
        $categories = [];
        $source = "";
        $image = "";
        $status = 1;

        print($link . "\n");
        $crawler = GoutteFacade::request('GET', $link);

        //Title
        $titles = $crawler->filter('h3.title')->each(function ($node) {
            return $node->text();
        });
        foreach ($titles as $title) {
            $title = $title;
        }
        //Check story exist
        $hasStory = Story::where('title', $title)->first();
        if ($hasStory) {
            return null;
        }

        //Description
        $descriptions = $crawler->filter('.desc-text-full')->each(function ($node) {
            return $node->html();
        });

        foreach ($descriptions as $description) {
            $description = $description;
        }
        //image of story
        $images = $crawler->filter('.books>.book>img')->each(function ($node) {
            return $node->attr('src');
        });

        foreach ($images as $image) {
            $image = $image;
        }

        try {
            if($image == null)
            {
                return $this->saveStory($link);
            }
            $content = file_get_contents($image);
            $extension = pathinfo(parse_url($image, PHP_URL_PATH), PATHINFO_EXTENSION);
            $filename = md5(time() . '_' . Str::random(10)) . pathinfo(parse_url($image, PHP_URL_PATH), PATHINFO_FILENAME);;
            $filePath = 'public/';

            $uploaded = Storage::disk('local')->put($filePath . $filename . "." . $extension, $content);
            $image_url = Storage::disk('local')->url($filePath . $filename . "." . $extension);
        } catch (Exception $e) {
            $image_url = "";
        }


        //Author & Categories
        $itemprop = $crawler->filter('.info>div>a')->each(function ($node) {
            return $node->text();
        });

        $count = 0;
        foreach ($itemprop as $item) {
            //Get first itemprop is author
            if ($count == 0) {
                $author = $item;
                $count++;
            } else {
                //Multiple categories
                array_push($categories, $item);
            }
        }

        //Source
        $sources = $crawler->filter('.info>div>.source')->each(function ($node) {
            return $node->text();
        });

        foreach ($sources as $source) {
            $source = $source;
        }

        //status
        $status_not_full = $crawler->filter('.info>div>span.text-primary')->each(function ($node) {
            return $node->text();
        });

        if($status_not_full)
        {
            $status = 0;
        }

        //Save data to database
        $author_id = getAuthorByName($author)->id;
        Story::updateOrCreate(
            [
                'title' => $title,
            ],
            [
                'description' => $description,
                'image' => $image_url,
                'author_id' => $author_id,
                'source' => $source,
                'status' => $status
            ]
        );

        $story = getStoryByName($title);
        $story_id = $story->id;
        StoryCategories::where('story_id', $story_id)->delete();
        foreach ($categories as $category) {
            $category = getCategoryByName($category);
            if (!$category) {
                continue;
            }
            $category_id = $category->id;
            StoryCategories::create(
                [
                    'story_id' => $story_id,
                    'category_id' => $category_id,
                ]
            );
        }

        //Save chapter to database
        $arrName = [];
        $check = true;
        $pageNumber = 1;

        do {
            try {
                $crawlerChapters = GoutteFacade::request('GET', $link . "/trang-" . $pageNumber);
                $chapters = $crawlerChapters->filter('ul.list-chapter>li>a')->each(function ($node) {
                    return $node->text();
                });

                foreach ($chapters as $chapter_namne) {
                    if (in_array($chapter_namne, $arrName)) {
                        $check = false;
                    } else {
                        array_push($arrName, $chapter_namne);
                    }
                }

                if (!$check) {
                    break;
                }
                //Read detail story
                $chapter_links = $crawlerChapters->filter('ul.list-chapter>li>a')->each(function ($node) {
                    return $node->attr("href");
                });
                foreach ($chapter_links as $chapter_link) {
                    $crawlerChapter = GoutteFacade::request('GET', $chapter_link);
                    //Get title
                    $chapter_name = $crawlerChapter->filter('.chapter-title')->each(function ($node) {
                        return $node->text();
                    })[0];
                    //Get desc
                    $chapter_desc = $crawlerChapter->filter('.chapter-c')->each(function ($node) {
                        return $node->html();
                    })[0];
                    Chapter::updateOrCreate([
                        'story_id' => $story_id,
                        'title' => $chapter_name,
                    ], [
                        'description' => $chapter_desc
                    ]);
                }
            } catch (Exception $e) {
            }
            $pageNumber++;
        } while ($check);

        // break;

    }
}
