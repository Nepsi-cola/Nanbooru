<?php

declare(strict_types=1);

namespace Shimmie2;

final class ArchiveFileHandlerTest extends ShimmiePHPUnitTestCase
{
    public function testArchiveHander(): void
    {
        self::log_in_as_user();
        system("zip -q tests/test.zip tests/pbx_screenshot.jpg tests/favicon.png");
        $this->post_image("tests/test.zip", "a z");

        $images = Search::find_images();
        self::assertEquals(2, count($images));
        self::assertEquals("a tests z", $images[0]->get_tag_list());
        self::assertEquals("a tests z", $images[1]->get_tag_list());
    }

    public function tearDown(): void
    {
        if (file_exists("tests/test.zip")) {
            unlink("tests/test.zip");
        }

        parent::tearDown();
    }
}
