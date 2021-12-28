<?php

namespace App\Entity;

use App\Repository\ScreenshotRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;

#[ORM\Entity(repositoryClass: ScreenshotRepository::class)]
class Screenshot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $url;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private $width;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private $height;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private $output;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private $file_type;

    #[ORM\Column(type: 'boolean')]
    private $lazy_load;

    #[ORM\Column(type: 'boolean')]
    private $dark_mode;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private $grayscale;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $delay;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $user_agent;

    #[ORM\Column(type: 'boolean')]
    private $full_page;

    #[ORM\Column(type: 'boolean')]
    private $fail_on_error;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private $clip_x;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private $clip_y;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private $clip_w;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private $clip_h;

    #[ORM\Column(type: 'datetime')]
    #[Ignore]
    private $created_on;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $filename;

    #[ORM\Column(type: 'boolean', nullable: true)]
    #[Ignore]
    private $success;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(?int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(?int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getOutput(): ?string
    {
        return $this->output;
    }

    public function setOutput(?string $output): self
    {
        $this->output = $output;

        return $this;
    }

    public function getFileType(): ?string
    {
        return $this->file_type;
    }

    public function setFileType(?string $file_type): self
    {
        $this->file_type = $file_type;

        return $this;
    }

    public function getLazyLoad(): ?bool
    {
        return $this->lazy_load;
    }

    public function setLazyLoad(bool $lazy_load): self
    {
        $this->lazy_load = $lazy_load;

        return $this;
    }

    public function getDarkMode(): ?bool
    {
        return $this->dark_mode;
    }

    public function setDarkMode(bool $dark_mode): self
    {
        $this->dark_mode = $dark_mode;

        return $this;
    }

    public function getGrayscale(): ?int
    {
        return $this->grayscale;
    }

    public function setGrayscale(?int $grayscale): self
    {
        $this->grayscale = $grayscale;

        return $this;
    }

    public function getDelay(): ?int
    {
        return $this->delay;
    }

    public function setDelay(?int $delay): self
    {
        $this->delay = $delay;

        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->user_agent;
    }

    public function setUserAgent(?string $user_agent): self
    {
        $this->user_agent = $user_agent;

        return $this;
    }

    public function getFullPage(): ?bool
    {
        return $this->full_page;
    }

    public function setFullPage(bool $full_page): self
    {
        $this->full_page = $full_page;

        return $this;
    }

    public function getFailOnError(): ?bool
    {
        return $this->fail_on_error;
    }

    public function setFailOnError(bool $fail_on_error): self
    {
        $this->fail_on_error = $fail_on_error;

        return $this;
    }

    public function getClipX(): ?int
    {
        return $this->clip_x;
    }

    public function setClipX(?int $clip_x): self
    {
        $this->clip_x = $clip_x;

        return $this;
    }

    public function getClipY(): ?int
    {
        return $this->clip_y;
    }

    public function setClipY(?int $clip_y): self
    {
        $this->clip_y = $clip_y;

        return $this;
    }

    public function getClipW(): ?int
    {
        return $this->clip_w;
    }

    public function setClipW(?int $clip_w): self
    {
        $this->clip_w = $clip_w;

        return $this;
    }

    public function getClipH(): ?int
    {
        return $this->clip_h;
    }

    public function setClipH(?int $clip_h): self
    {
        $this->clip_h = $clip_h;

        return $this;
    }

    public function getCreatedOn(): ?\DateTimeInterface
    {
        return $this->created_on;
    }

    public function setCreatedOn(\DateTimeInterface $created_on): self
    {
        $this->created_on = $created_on;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(?string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getSuccess(): ?bool
    {
        return $this->success;
    }

    public function setSuccess(?bool $success): self
    {
        $this->success = $success;

        return $this;
    }

    #[Ignore]
    public function fetchParams(): array
    {
        $params = [];
        foreach ($this as $key => $value) {
            if (
                isset($value)                                               // check if not null
                && $value                                                   // we don't show false values
                && !in_array($key, ['id', 'url', 'created_on', 'filename', 'success']) // no special fields
            ) {
                $params[$key] = $value;
            }
        }

        return $params;
    }
}
