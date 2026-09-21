# Video encoding

How to turn a showreel export into web-ready files for the homepage hero and the service page showreel, and for the full showreel that opens when someone clicks the showreel button, and why it works this way.

## Quick start

```bash
# 1. First time on this machine only
pnpm install

# 2. Put the master export in build/masters/ (create the folder if needed)

# 3. Encode it
pnpm encode "build/masters/My Showreel.mp4" service-showreel --skip-webm
```

4. Check the numbers the script prints (see [Reading the output](#reading-the-output)) and watch the result full screen.
5. In WordPress go to **Media → Add New**, upload the `.mp4` and the `-poster.webp` from `build/video/`, then select them in the page's ACF fields (see [Which file goes where](#which-file-goes-where)).

## How it works

`pnpm encode` is not a built-in command. It is a script entry in [`package.json`](package.json):

```json
"encode": "node scripts/encode-video.mjs"
```

So `pnpm encode ...` runs [`scripts/encode-video.mjs`](scripts/encode-video.mjs), a small Node script in this repo. It uses two dev dependencies:

- **`ffmpeg-static`**: a copy of ffmpeg shipped as an npm package. Nothing needs installing with Homebrew or on the server.
- **`sharp`**: converts the poster frame to WebP.

It only runs when you call it. `pnpm build` and `pnpm dev` never touch it.

`package.json` also contains:

```json
"pnpm": { "onlyBuiltDependencies": ["ffmpeg-static"] }
```

Leave that in place. pnpm blocks install scripts by default, and `ffmpeg-static` downloads the ffmpeg binary in its install script. Without this line `pnpm install` finishes without error but the binary is never downloaded, and the encoder fails.

## Folders

| Folder | What goes in it | In git? |
|---|---|---|
| `build/masters/` | Original exports from the video editor | No |
| `build/video/` | Encoder output, ready to upload | No |

All of `build/` is gitignored, so files in there never get committed, whatever they are called. Don't leave master videos in the project root. They are not ignored there and would be picked up by the next commit.

`build/` exists only on your machine and is not backed up with the repo. If a master only exists here, keep a copy somewhere else too.

## Command reference

```bash
pnpm encode <input> <output-name> [options]
```

- `<input>`: path to the master. Quote it if the filename has spaces.
- `<output-name>`: used for the output filenames, for example `service-showreel`.

| Option | What it does |
|---|---|
| `--skip-webm` | Don't make a WebM. **Use this normally**, since the site serves MP4 only. |
| `--skip-mp4` | Don't make an MP4. |
| `--poster-only` | Only extract the poster image. Takes seconds. |
| `--full` | Encode the full showreel for the modal instead of a background loop. See [Full showreel](#full-showreel). |
| `--crf <n>` | MP4 quality. Default `18`. Lower means higher quality and a bigger file. |
| `--vp9-crf <n>` | WebM quality. Default `24`. |
| `--outdir <dir>` | Where to write the files. Default `build/video`. |

Options can go before or after the file names, and `pnpm encode -- ...` works too.

Examples:

```bash
# Normal use
pnpm encode "build/masters/Service reel v2.mp4" service-showreel --skip-webm

# Just redo the poster
pnpm encode "build/masters/Service reel v2.mp4" service-showreel --poster-only

# Output looks slightly soft: push quality up
pnpm encode "build/masters/Service reel v2.mp4" service-showreel --skip-webm --crf 16
```

Run `pnpm encode` with no arguments to print the usage line.

## What you get

For `pnpm encode "build/masters/x.mp4" service-showreel`:

| File | Use it? |
|---|---|
| `build/video/service-showreel.mp4` | Yes, upload it |
| `build/video/service-showreel-poster.webp` | Yes, upload it |
| `build/video/service-showreel.webm` | No (only made without `--skip-webm`) |

On an Apple Silicon Mac, a 16-second 1080p clip at 60 fps took about 20 seconds for the MP4 and about 35 seconds for the WebM.

## Reading the output

The script prints something like this:

```
source: Home page version-v3.mp4
  1920x1080  15.77s  30fps  h264  audio: none
  source      29.22 MB    14.83 Mbps   Home page version-v3.mp4

outputs:
  mp4 c18      8.22 MB     4.17 Mbps   hero-home.mp4
  poster       0.06 MB        ? Mbps   hero-home-poster.webp
```

Check three things:

1. **Resolution and duration** match the master.
2. **The MP4 is under 10 Mbps and under 30 MB.** WordPress rejects anything above either limit (see [Upload guard](#upload-guard)).
3. **It looks right.** Open the MP4 and the master side by side, full screen. Look for banding in smooth gradients and smeared film grain. If you see either, re-run with `--crf 16`.

The numbers above are from the launch showreel: 29.2 MB went down to 8.2 MB with no visible difference.

## Which file goes where

| Page | ACF tab | Field | File |
|---|---|---|---|
| Homepage | HERO | Small autoplay video (`hero_autoplay_video`) | `.mp4` |
| Homepage | HERO | Poster frame (`hero_poster`) | `-poster.webp` |
| Homepage | HERO | Poster frame (mobile, optional) (`hero_poster_mobile`) | Optional portrait image |
| Service | Showreel | Small autoplay video (`showreel_autoplay_video`) | `.mp4` |
| Service | Showreel | Poster frame (`showreel_poster`) | `-poster.webp` |
| Service | Showreel | Poster frame (mobile, optional) (`showreel_poster_mobile`) | Optional portrait image |

Leave the WebM fields empty.

The same uploaded video can be selected on several pages. Reusing one file is better than uploading copies, because browsers only download and cache it once.

**The poster is required** whenever a video is set. WordPress won't save the page without it.

**The mobile poster is optional.** Phones never download the video, they only see a still. If this field is empty they get the normal 16:9 poster, cropped to fill a tall screen, which loses most of the frame. For a better result, crop a portrait still from the video in an image editor and upload it here. The encoder does not make this image.

## Full showreel

The showreel button opens the full reel, with sound, in a pop-up player. The homepage and the service pages share **one reel**. It isn't set in WordPress: it's a file in the theme, so replacing it needs a developer.

**Never put the export from the video editor straight into `media/`.** Exports are usually 3–4 times the bitrate the web needs. For example, the launch reel was 95 MB at 15 Mbps before encoding and 28 MB at 4.4 Mbps after, with no visible difference. `media/` holds only the encoder's output.

### Step by step

The example uses the name `showreel-2027`. Pick a new name for every new reel (see below).

1. **Put the export in `build/masters/`**, for example `build/masters/showreel-2027-master.mp4`.
2. **Encode it:**
   ```bash
   pnpm encode build/masters/showreel-2027-master.mp4 showreel-2027 --full
   ```
   This takes about 30 seconds for a one-minute reel, and writes two files to `build/video/`:

   | File | What it is |
   |---|---|
   | `showreel-2027.mp4` | 1080p. Everyone gets this except the cases below. |
   | `showreel-2027-720.mp4` | 720p. For phones, data-saver mode and slow connections. |

3. **Check the numbers it prints.** The 1080p file should be under about 5.5 Mbps, and the 720p under about 2.8 Mbps. The source line also says `audio: yes` or `audio: none`. If it says none and the reel should have sound, re-export it from the editor with audio.
4. **Watch `build/video/showreel-2027.mp4`** full screen next to the master.
5. **Copy both files into `media/`** in the theme. Locally that's `media/` next to `dist/`. On the server it's `wp-content/themes/<theme>/media/`, uploaded by SFTP. The files are gitignored, so a normal deploy won't carry them.
6. **Point the theme at the new name.** Set `FDRY_SHOWREEL_BASENAME` in [`library/function-dev.php`](library/function-dev.php) to `media/showreel-2027` and deploy that change.
7. **Check it:** open `https://<site>/wp-content/themes/<theme>/media/showreel-2027.mp4` in a browser. It should play. Then the showreel button appears on the homepage and service pages.
8. **Delete the previous reel's files** from `media/` on the server.

### What the encoder does

Both files keep the audio, which the background loops remove. They also have a keyframe at least every 2 seconds, so jumping around the timeline is quick. The bitrate is capped at about 5.5 Mbps for 1080p and 2.8 Mbps for 720p, so busy scenes can't spike above what a normal connection keeps up with. A source larger than 1080p is scaled down to 1080p, and a smaller source is never scaled up. Quality defaults to `--crf 19`, and the 720p file is always one step lower.

The launch reel (51 s, 1080p, 30 fps) came out at 28 MB and 4.4 Mbps for 1080p, and at 13 MB and 2.1 Mbps for 720p. Compared with the master, the 1080p file scored an SSIM of 0.994, where 1.0 means identical.

**Use a new name for every new reel** rather than overwriting the old files. Browsers and Cloudflare cache video files for a long time, and a new name means nobody gets the old reel from cache.

**No file, no button.** If `showreel-2027.mp4` isn't on the server, the showreel button doesn't appear on any page. The 720p file is optional. Without it, everyone gets the 1080p file.

How the player loads is described in [`FRONTEND.md`](FRONTEND.md) under "Showreel modal". In short:
- Nothing downloads with the page.
- Hovering over the button fetches about 0.3 MB.
- Clicking starts playback almost immediately.
- Closing the player stops the download.

## If the developer doesn't have this repo

The upload error in WordPress gives this brief to forward. Anyone with ffmpeg can run it:

```bash
ffmpeg -i INPUT -an -c:v libx264 -profile:v high -level 4.2 -preset veryslow -crf 18 -pix_fmt yuv420p -movflags +faststart hero.mp4
ffmpeg -i INPUT -frames:v 1 -q:v 1 poster.png
```

`-movflags +faststart` is the part that must not be left out. It's what makes the video start playing before it has fully downloaded.

## Upload guard

Editors can't run the encoder, so WordPress checks every file selected in the autoplay video fields and blocks the save if the file:

- is larger than **30 MB**
- is above **10 Mbps**, which means it's a master export rather than a web encode
- is an MP4 **without faststart**

The error message explains the problem and includes the ffmpeg brief above. The check runs in plain PHP ([`library/function-dev.php`](library/function-dev.php), `fdry_validate_background_video()`), because the host has no ffmpeg. If it can't read a file for some reason, it lets it through rather than blocking a valid upload.

The limits are the `FDRY_HERO_MAX_BITRATE_MBPS` and `FDRY_HERO_MAX_BYTES` constants in the same file.

## What we did and why

### Removed Vimeo

The hero used to play the showreel through a Vimeo embed. It was slow, and raising the quality made it worse:

- The Vimeo iframe was created by JavaScript after the page loaded, so the browser couldn't start fetching it early.
- Before any video played, the browser had to load Vimeo's player page and scripts.
- Vimeo's player starts at low quality and then steps up, so the first seconds always looked soft, whatever quality was uploaded.

The video is now served directly from our own Media Library as a plain `<video>`.

### Didn't use HLS

HLS splits a video into chunks and switches quality as the connection changes. It's built for long videos. For a short silent loop it adds extra requests before the first frame, needs roughly 60 KB of extra JavaScript, and brings back the "starts soft" behaviour we wanted to get rid of. A single well-encoded MP4 is faster here.

We weighed it again for the full showreel, which is longer and has sound, and still chose MP4. For a 1–3 minute reel, the one real gain would be switching to lower quality mid-play when a connection weakens. The costs were an extra player library, a change to how the whole site's JavaScript loads, hundreds of files to upload per reel, and extra server and Cloudflare settings. Instead, the 720p file covers phones and slow connections, and it's chosen when the player opens. If the reel ever grows past about 5 minutes or goes 4K, look at HLS again.

### Media Library, not Cloudflare R2

The files live in the WordPress Media Library, so there's no extra service to set up and editors can swap videos themselves.

The site runs behind Cloudflare. Cloudflare's terms allow them to limit sites that serve video through their CDN unless the files are hosted on one of their own products, such as R2. That rule targets sites using Cloudflare as a free video host at scale, and one hero loop on a portfolio site isn't that. Leave Cloudflare's settings at their defaults. If it ever becomes a problem, moving the videos to R2 is a contained change.

### Kept the quality

The master's grading is the quality we want, so the settings are chosen to look identical to it, not to make the smallest file. Master exports are usually far bigger than they need to be, so the file still shrinks a lot without visible loss.

The resolution, frame rate and length are never changed. The audio track is removed, since the video always plays muted.

### MP4 only, no WebM

WebM (VP9) is usually smaller than MP4 at the same quality. We tested it on this footage and it wasn't. Compared with the original using SSIM, a score where 1.0 means identical:

| File | Size | SSIM |
|---|---|---|
| MP4, CRF 18 | 8.2 MB | 0.9965 |
| WebM, CRF 24 | 6.2 MB | 0.9691 |
| WebM, CRF 10 | 13.9 MB | 0.9705 |

The WebM never got close to the MP4's quality, even at a larger size. Browsers that support WebM would pick it first, so shipping both would have given most visitors the worse version. The WebM fields still exist in case a future video tests differently.

The second homepage video (`home-short`, 60 fps) gave the same result:

| File | Size | SSIM |
|---|---|---|
| MP4, CRF 18 | 10.4 MB | 0.9962 |
| WebM, CRF 24 | 10.1 MB | 0.9754 |

### Poster first, video second

On every page load:

1. The poster image shows immediately. On the homepage it's also preloaded, so it's the first thing to appear.
2. The video starts downloading, and fades in over the poster once it's ready.

The video is **never downloaded** on screens 768px wide or less, when the visitor has "reduce motion" turned on, or when their browser is in data-saver mode. They see the poster only. On the service page the video also waits until the visitor scrolls near it.

Technical details for developers are in [`FRONTEND.md`](FRONTEND.md) under "Hero / showreel video".

## Troubleshooting

**The encoder fails straight away, or says ffmpeg can't be found.**
Run `pnpm install`, and check that `onlyBuiltDependencies` is still in `package.json`. To confirm the binary exists:

```bash
node -e "const p=require('ffmpeg-static'); console.log(p, require('fs').existsSync(p))"
```

It should print a path followed by `true`.

**WordPress rejects the upload.**
The error says which limit was hit. Re-encode from the master with `pnpm encode`. Don't re-encode a file that was already compressed, because quality drops each time.

**The poster is black.**
The poster is always the video's first frame. If the edit fades in from black, the poster will be black too. Export a still from a later point in the video and upload that as the poster instead.

**The encode looks soft next to the master.**
Re-run with `--crf 16`. The file will be bigger.

**It's slow.**
Make sure you're using `--skip-webm`. A short clip should take well under a minute.
