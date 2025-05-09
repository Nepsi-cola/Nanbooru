<?php

declare(strict_types=1);

namespace Shimmie2;

use function MicroHTML\{BR, INPUT, P, SMALL, emptyHTML};
use function MicroHTML\{TABLE, TD, TR};

class ReplaceFileTheme extends Themelet
{
    /**
     * Only allows 1 file to be uploaded - for replacing another image file.
     */
    public function display_replace_page(int $image_id): void
    {
        $tl_enabled = (Ctx::$config->get(UploadConfig::TRANSLOAD_ENGINE) !== "none");
        $accept = $this->get_accept();

        $max_size = Ctx::$config->get(UploadConfig::SIZE);
        $max_kb = to_shorthand_int($max_size);

        $image = Image::by_id_ex($image_id);
        $thumbnail = $this->build_thumb($image);

        $form = SHM_FORM(make_link("replace/".$image_id), multipart: true);
        $form->appendChild(emptyHTML(
            TABLE(
                ["id" => "large_upload_form", "class" => "form"],
                TR(
                    TD("File"),
                    TD(INPUT(["name" => "data", "type" => "file", "accept" => $accept]))
                ),
                $tl_enabled ? TR(
                    TD("or URL"),
                    TD(INPUT(["name" => "url", "type" => "text", "value" => @$_GET['url']]))
                ) : null,
                TR(TD("Source"), TD(["colspan" => 3], INPUT(["name" => "source", "type" => "text"]))),
                TR(TD(["colspan" => 4], INPUT(["id" => "uploadbutton", "type" => "submit", "value" => "Post"]))),
            )
        ));

        $html = emptyHTML(
            P(
                "Replacing Post ID $image_id",
                BR(),
                "Please note: You will have to refresh the post page, or empty your browser cache."
            ),
            $thumbnail,
            BR(),
            $form,
            $max_size > 0 ? SMALL("(Max file size is $max_kb)") : null,
        );

        Ctx::$page->set_title("Replace File");
        $this->display_navigation();
        Ctx::$page->add_block(new Block("Upload Replacement File", $html, "main", 20));
    }

    protected function get_accept(): string
    {
        return ".".join(",.", DataHandlerExtension::get_all_supported_exts());
    }
}
