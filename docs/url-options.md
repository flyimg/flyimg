# URL Options

This document lists and describes the full list of options available to be passed as parameters in the URL.

The server is setup to perform operations based on the following URL pattern.

```html
https://server.address.io/[process-type]/[image_options]/[path_to_image]
```

for example:

```html
https://demo.flyimg.io/upload/w_500/https://flyimg.io/demo-images/Citroen-DS.jpg
```

Explanation:

## process-type: _upload_ or _path_

There are 2 main proceses you can do for images.

The first: **upload**, grabs an image from a URL, transforms it, saves it, and serves it, as an image, with all the appropriate headers.

The second: **path**, grabs an image from a URL, transforms it, saves it, and returns the absolute path to the image as a string, in the body of the response.

---

## path_to_image

It's the first operation the server does, it will try to get an image from the URI in the path, it can be relative to the local server, or absolute to the internet.

**example:** `https://flyimg.io/demo-images/Citroen-DS.jpg`

---

## Alternative sources and POST uploads

In addition to fetching images from an HTTP/HTTPS URL, Flyimg supports:

- S3 references: `s3://<bucket>/<key>`
- Direct POST uploads with the request body

### POST uploads (recommended for large inputs)

- Endpoint: `POST /upload/{image_options}`
- Supported bodies:
  - `application/octet-stream` or any `image/*`: raw binary body
  - `application/json`: `{ "base64": "<BASE64>" }` or `{ "dataUri": "data:<mime>;base64,<data>" }`

Examples:

```bash
curl -X POST -H "Content-Type: application/octet-stream" \
  --data-binary @tests/testImages/square.png \
  http://localhost:8080/upload/w_300,o_jpg -o out.jpg
```

```bash
B64=$(base64 -i tests/testImages/square.jpg)
curl -X POST -H "Content-Type: application/json" \
  -d "{\"base64\":\"$B64\"}" \
  http://localhost:8080/upload/w_300,o_png -o out.png
```

### S3 sources

Fetch from S3 using a simple URI:

```
/upload/w_300/s3://my-bucket/path/to/image.png
```

Configuration for S3 endpoint/region is *required*, see configuration docs below.

## image_options

Here you set all the transformations and output settings you want to apply to the image you are fetching.

Most of these options are ImageMagick flags, many can get pretty advanced, use the [ImageMagick docs](http://www.imagemagick.org/script/command-line-options.php).
We put a lot of defaults in place to prevent distortion, bad quality, weird cropping and unwanted paddings.

The script **does a lot of sanitizing** of the parameters, so many options will not work or have to be carefully escaped. Priority is given to safety and eas of use.

## Basic geometry

### `w` : width

`int`
_Default:_ `null`
_Description:_ Sets the target width of the image. If not set, width will be calculated in order to keep aspect ratio.

**example:`w_100`**

`w_100` : `https://demo.flyimg.io/upload/w_100/https://flyimg.io/demo-images/Citroen-DS.jpg`

![w_100](https://demo.flyimg.io/upload/w_100,o_jpg/https://flyimg.io/demo-images/Citroen-DS.jpg)

### `h` : height

`int`
_Default:_ `null`
_Description:_ Sets the target height of the image. If not set, height will be calculated in order to keep aspect ratio.

**example:`h_100`**

`h_100` : `https://demo.flyimg.io/upload/h_100/https://flyimg.io/demo-images/Citroen-DS.jpg`

![h_100](https://demo.flyimg.io/upload/h_100,o_jpg/https://flyimg.io/demo-images/Citroen-DS.jpg)

### Using width AND height

**example:`h_300,w_300`**
By default setting width and height together, works like defining a rectangle that will define a **max-width** and **max-height** and the image will scale propotionally to fit that area without cropping.

<!-- in the future put example images here-->

By default; width, height, or both will **not scale up** an image that is smaller than the defined dimensions.

<!-- in the future put example images here-->

`h_300,w_300` : `https://demo.flyimg.io/upload/h_300,w_300/https://flyimg.io/demo-images/Citroen-DS.jpg`

![h_300,w_300](https://demo.flyimg.io/upload/h_300,w_300,o_jpg/https://flyimg.io/demo-images/Citroen-DS.jpg)

### `c` : crop

`bool`
_Default:_ `false`
_Description:_ When both width and height are set, this allows the image to be cropped, so it fills the **width x height** area.

**example:`c_1`**

`c_1,h_400,w_400` : `https://demo.flyimg.io/upload/c_1,h_400,w_400/https://flyimg.io/demo-images/Citroen-DS.jpg`

![c_1,h_400,w_400](https://demo.flyimg.io/upload/c_1,h_400,w_400,o_jpg/https://flyimg.io/demo-images/Citroen-DS.jpg)

### `g` : gravity

`string`
_Default:_ `Center`
_Description:_ When crop is applied, changing the gravity will define which part of the image is kept inside the crop area.
The basic options are: `NorthWest`, `North`, `NorthEast`, `West`, `Center`, `East`, `SouthWest`, `South`, `SouthEast`.

**example:`g_West`**

### `t` : text

`string`
_Default:_ `null`
_Description:_ Add text to the image (watermark).
Use the gravity option `g` to define where the text will be placed.

**example:`t_Hello`**

### `tc` : text-color

`string`
_Default:_ `white`
_Description:_ Set the color of the text.
For the hex code, the hash `#` character should be replaced by `%23`

**example:`tc_%23ff4455`, `tc_red`**

### `ts` : text-size

`int`
_Default:_ `12`
_Description:_ Set the size of the text.

**example:`ts_24`**

### `ts` : text-size

`int`
_Default:_ `12`
_Description:_ Set the size of the text.

**example:`ts_24`**

### `tbg` : text-bg

`string`
_Default:_ `null`
_Description:_ Set the background color of the text.
For the hex code, the hash `#` character should be replaced by `%23`

**example:`tbg_%23ff4455`, `tbg_red`**

![t_by%20Flyimg,tc_%23bcc7c9,ts_44,tbg_%234f8400,g_SouthEast](https://demo.flyimg.io/upload/w_600,t_by%20Flyimg,tc_%23bcc7c9,ts_44,tbg_%234f8400,g_SouthEast,o_jpg/https://flyimg.io/demo-images/Citroen-DS.jpg)

### `r` : rotate

`string`
_Default:_ `null`
_Description:_ Apply image rotation (using shear operations) to the image.

**example: `r_90`, `r_-180`,...**

`r_45` : `https://demo.flyimg.io/upload/r_-45,w_400,h_400/https://flyimg.io/demo-images/Citroen-DS.jpg`

![r_45](https://demo.flyimg.io/upload/r_-45,w_400,h_400,o_jpg/https://flyimg.io/demo-images/Citroen-DS.jpg)

---

## Output file formats

### `o` : output

`string`
_Default:_ `auto`
_Description:_ Output format requested, for example you can force the output as jpeg file in case of source file is png. The default `auto` will try to output the best format for the requesting browser, falling back to the same format as the source image or finally with a fallback to **jpg**.

If `input` is passed, no "optimal" format will be attempted. Flyimg will try to respond with the source format or fallback to `jpg`.

**example:`o_auto`,`o_input`,`o_png`,`o_webp`,`o_jpeg`,`o_jpg`**

### `q` : quality

`int` (0-100)
_Default:_ `90`
_Description:_ Sets the compression level for the output image. Your best results will be between **70** and **95**.

**example:`q_100`,`q_75`,...**

`q_30` : `https://demo.flyimg.io/upload/q_30,w_600/https://flyimg.io/demo-images/Citroen-DS.jpg`

![q_30](https://demo.flyimg.io/upload/q_30,w_600,o_jpg/https://flyimg.io/demo-images/Citroen-DS.jpg)

`q_100` : `https://demo.flyimg.io/upload/q_100,w_600/https://flyimg.io/demo-images/Citroen-DS.jpg`

![q_100](https://demo.flyimg.io/upload/q_100,w_600,o_jpg/https://flyimg.io/demo-images/Citroen-DS.jpg)

### `webpl` : webp-lossless

`int`
_Default:_ `0`
_Description:_ If output is set to webp, it will default to lossy compression, but if you want lossless webp encoding you have to set it to 1.

**example:`webpl_1`**

`webpl_1` : `https://demo.flyimg.io/upload/webpl_1,w_600/https://flyimg.io/demo-images/Citroen-DS.jpg`

![webpl_1](https://demo.flyimg.io/upload/webpl_1,w_600,o_webp/https://flyimg.io/demo-images/Citroen-DS.jpg)

### `webpm` : webp-method

`string`
_Default:_ `4`
_Description:_ the compression method to use. It controls the trade off between encoding speed and the compressed file size and quality. Possible values range from 0 to 6. Default value is 4. When higher values are utilized, the encoder spends more time inspecting additional encoding possibilities and decide on the quality gain. Lower value might result in faster processing time at the expense of larger file size and lower compression quality.

**example:`webpm_6`**

## Refresh or re-fetch source image

### `rf` : refresh

_Default:_ `false`
_Description:_ When this parameter is 1, it will force a re-request of the original image and run it through the transformations and compression again. It will delete the local cached copy.

**example:`rf_1`**

The nginx server will send headers to prevent caching of this request.

It will also send headers with the command done on the image + info returned by the command identity from Imagemagick.

---

## Fancy options

### `bg` : background

`color` (multiple formats)
_Default:_ `null`
_Description:_ Sets the background of the canvas for the cases where padding is added to the images. It supports hex, css color names, rgb.
Only css color names are supported without quotation marks.
For the hex code, the hash `#` character should be replaced by `%23`

**example:`bg_red`,`bg_%23ff4455`,`bg_rgb(255,120,100)`,...**

```sh
  [...] -background red ...
  [...] -background "#ff4455" -> "%23ff4455"
  [...] -background "rgb(255,120,100)" ...
```

`https://demo.flyimg.io/upload/r_45,w_400,h_400,bg_red/https://flyimg.io/demo-images/Citroen-DS.jpg`

![bg_red](https://demo.flyimg.io/upload/r_45,w_400,h_400,bg_red,o_jpg/https://flyimg.io/demo-images/Citroen-DS.jpg)

### `st` : strip

`int`
_Default:_ `1`
_Description:_ removes exif data and additional color profile. Leaving your image with the default sRGB color profile.

**example:`st_1`**

### `ao` : auto-orient

`int`
_Default:_ `0`
_Description:_ Adjusts an image so that its orientation is suitable for viewing (i.e. top-left orientation).

**example:`ao_1`**

### `rz` : resize

`int` _Default:_ `null`
_Description:_ The alternative resizing method to -thumbnail.

**example:`rz_1`**

### `moz` : mozjpeg

_Default:_ `1`
_Description:_ Use moz-jpeg compression library, if `0` it fallsback to the default ImageMagick compression algorithm.

### `unsh` : unsharp

`{radius}x{sigma}[+gain][+threshold]`
_Default:_ `null`
_Description:_ Sharpens an image (despite the unfortunate naming). It basically sharpens via subtracting a blurred, low contrast, version of the image to the image itself. For more details check [Imagemagick docs](http://www.imagemagick.org/script/command-line-options.php#unsharp).

**example 1:`unsh_0x6`**  
**example 2:`unsh_0.25x0.25+8+0.065`**

### `sh` : sharpen

`{radius}x{sigma}`
_Default:_ `null`
_Description:_ Use a Gaussian operator of the given radius and standard deviation (sigma). For more details check [Imagemagick docs](http://www.imagemagick.org/script/command-line-options.php#sharpen).

**example 1:`sh_3`**  
**example 2:`sh_0x5`**

### `blr` : blur

`{radius}x{sigma}`
_Default:_ `null`
_Description:_ Apply Blur on a image. For more details check [Imagemagick docs](http://www.imagemagick.org/script/command-line-options.php#blur).

**example 1:`blr_2`**  
**example 2:`blr_1x2`**

### `f` : filter

`string`
_Default:_ `Lanczos`
_Description:_ Resizing algorithm, Triangle is a smoother lighter option

**example:`f_Triangle`**

### `sc` : scale

_Default:_ `null`
_Description:_ The "-scale" resize operator is a simplified, faster form of the resize command. Useful for fast exact scaling of pixels.

**example:`sc_1`**

### `fc` : face-crop

`int`
_Default:_ `0`
_Description:_ Using [facedetect](https://github.com/wavexx/facedetect) repository to detect faces and passe the coordinates to ImageMagick to crop.

**example:`fc_1`**

`fc_1` : `https://demo.flyimg.io/upload/fc_1/http://facedetection.jaysalvat.com/img/faces.jpg`

![fc_1](https://demo.flyimg.io/upload/fc_1,o_jpg/http://facedetection.jaysalvat.com/img/faces.jpg)

### `fcp` : face-crop-position

`int`
_Default:_ `0`
_Description:_ When using the Face crop option and when the image contain more than one face, you can specify which one you want get cropped

**example:`fcp_1`,`fcp_0`,...**

`fcp_2` : `https://demo.flyimg.io/upload/fc_1,fcp_2/http://facedetection.jaysalvat.com/img/faces.jpg`

![fcp_2](https://demo.flyimg.io/upload/fc_1,fcp_2/http://facedetection.jaysalvat.com/img/faces.jpg)

### `fb` : face-blur

`int`
_Default:_ `0`
_Description:_ Apply blur effect on faces in a given image

**example:`fb_1`**

`fb_1` : `https://demo.flyimg.io/upload/fb_1/http://facedetection.jaysalvat.com/img/faces.jpg`

![fb_1](https://demo.flyimg.io/upload/fb_1,o_jpg/http://facedetection.jaysalvat.com/img/faces.jpg)

### `smc` : smart-crop

`int`
_Default:_ `0`
_Description:_ Use smart crop to get the best possible crop of the image.

**example:`smc_1`**

- Image: `https://images.pexels.com/photos/1280553/pexels-photo-1280553.jpeg`
- Width: 500
- Smart Crop: `smc_1`

**Without Smart Crop**

`https://demo.flyimg.io/upload/w_500/https://images.pexels.com/photos/1280553/pexels-photo-1280553.jpeg`

![lago_ranco](https://demo.flyimg.io/upload/w_500/https://images.pexels.com/photos/1280553/pexels-photo-1280553.jpeg)

**With Smart Crop Enabled**

`https://demo.flyimg.io/upload/w_500,smc_1/https://images.pexels.com/photos/1280553/pexels-photo-1280553.jpeg`

![lago_ranco](https://demo.flyimg.io/upload/w_500,smc_1/https://images.pexels.com/photos/1280553/pexels-photo-1280553.jpeg)

### `pdfp` : pdf-page

`int`
_Default:_ `1`
_Description:_ Sets the target page of the PDF. If not set, the default is page 1.

**example:`pdfp_1`**

- PDF: `https://flyimg.io/demo-images/lighthouses.pdf`
- Width: 200
- Height: 200
- Page: `pdfp_1`

`https://demo.flyimg.io/upload/w_200,h_200,pdfp_1/https://flyimg.io/demo-images/lighthouses.pdf`

![lago_ranco](https://demo.flyimg.io/upload/w_200,h_200,pdfp_1/https://flyimg.io/demo-images/lighthouses.pdf)

### `dnst` : density

`int`  
_Default:_ 72  
_Description:_ Sets the PDF density. If not set, the default is 72.

**example:`dnst_300`**

`dnst_300` : `https://demo.flyimg.io/upload/dnst_300,pdfp_1/https://flyimg.io/demo-images/lighthouses.pdf`


### `tm` : video-time

`string`
_Default:_ `00:00:01`
_Description:_ Sets the frame capture time duration point in the video. If not set, the default is 1 second. The format is `HH:MM:SS` OR `SS`

**example:`tm_00:00:05`**

- Video: `https://flyimg.io/demo-images/big_buck_bunny_720p_2mb.mp4`
- Width: 200
- Height: 200
- Time: `tm_00:00:05`

`https://demo.flyimg.io/upload/w_200,h_200,tm_00:00:05/https://flyimg.io/demo-images/big_buck_bunny_720p_2mb.mp4`

![lago_ranco](https://demo.flyimg.io/upload/w_200,h_200,tm_00:00:05/https://flyimg.io/demo-images/big_buck_bunny_720p_2mb.mp4)

### `clsp` : colorspace

`string`
_Default:_ `null`
_Description:_ Convert the image to a different colorspace.

**example:`clsp_Gray`**

- Image: `https://flyimg.io/demo-images/Citroen-DS.jpg`
- Width: 400
- Colorspace: `clsp_Gray`

`https://demo.flyimg.io/upload/w_400,clsp_Gray/https://flyimg.io/demo-images/Citroen-DS.jpg`

![lago_ranco](https://demo.flyimg.io/upload/w_400,clsp_Gray,o_jpg/https://flyimg.io/demo-images/Citroen-DS.jpg)

### `mnchr` : monochrome

`int`
_Default:_ `0`
_Description:_ Convert the image to monochrome.

**example:`mnchr_1`**

- Image: `https://flyimg.io/demo-images/Citroen-DS.jpg`
- Width: 400
- Monochrome: `mnchr_1`

`https://demo.flyimg.io/upload/w_400,mnchr_1/https://flyimg.io/demo-images/Citroen-DS.jpg`

![lago_ranco](https://demo.flyimg.io/upload/w_400,mnchr_1,o_jpg/https://flyimg.io/demo-images/Citroen-DS.jpg)

### `e` : extract

#### `p1x` : extract-top-x

#### `p1y` : extract-top-y

#### `p2x` : extract-bottom-x

#### `p2y` : extract-bottom-y

_Default:_ `null`

_Description:_ Extract and crop an image with given the x/y coordinates of each booth top and bottom.

### `sf` : sampling-factor

_Default:_ `1x1`
_Description:_ ...

### `ett` : extent

_Default:_ `null`
_Description:_ ... not ready

### `par` : preserve-aspect-ratio

`int`
_Default:_ `1`
_Description:_ If set to 0, when passing width and height to an image, the image will be distorted to fill the size of the rectangle defined by width and height.

### `pns` : preserve-natural-size

`int`
_Default:_ `1`
_Description:_ If set to 0 and if the source image is smaller than the target dimensions, the image will be stretched to the target size.

### `gf` : gif-frame

`int`
_Default:_ `0`
_Description:_ ...

### PDF options

Requires `ghostscript` installation in the Dockerfile.

#### `pdfp` : page number

`int`  
_Default:_ 1  
_Description:_ Sets the target page of the PDF. If not set, the default is page 1.

**example:`pdfp_2`**

`pdfp_2` : `https://demo.flyimg.io/upload/pdfp_2,w_300/https://flyimg.io/demo-images/lighthouses.pdf`

![pdfp_2](https://demo.flyimg.io/upload/pdfp_2,w_300/https://flyimg.io/demo-images/lighthouses.pdf)


### Video options

Requires `ffmpeg` installation in the Dockerfile.

#### `tm` : time

`string`  
_Default:_ `00:00:01`  
_Description:_ Sets the frame capture time duration point in the video. If not set, the default is 1 second. The format is `HH:MM:SS` OR `SS`

**example:`tm_00:00:05`**

`tm_00:00:05` : `https://demo.flyimg.io/upload/tm_00:00:05/https://flyimg.io/demo-images/big_buck_bunny_720p_2mb.mp4`

![tm_00:00:05](https://demo.flyimg.io/upload/tm_00:00:05/https://flyimg.io/demo-images/big_buck_bunny_720p_2mb.mp4)

You can also use a shorter syntax for the first 60 seconds.

**example:`tm_10`**

`tm_10` : `https://demo.flyimg.io/upload/tm_10/https://flyimg.io/demo-images/big_buck_bunny_720p_2mb.mp4`

---

## Options keys

```yml
options_keys:
  moz: mozjpeg
  q: quality
  o: output
  unsh: unsharp
  fc: face-crop
  fcp: face-crop-position
  fb: face-blur
  w: width
  h: height
  c: crop
  bg: background
  st: strip
  rz: resize
  g: gravity
  f: filter
  r: rotate
  sc: scale
  sf: sampling-factor
  rf: refresh
  ett: extent
  par: preserve-aspect-ratio
  pns: preserve-natural-size
  webpl: webp-lossless
```

## Default options values

```yml
default_options:
  mozjpeg: 1
  quality: 90
  output: auto
  unsharp: null
  face-crop: 0
  face-crop-position: 0
  face-blur: 0
  width: null
  height: null
  crop: null
  background: null
  strip: 1
  resize: null
  gravity: Center
  filter: Lanczos
  rotate: null
  scale: null
  sampling-factor: 1x1
  refresh: false
  extent: null
  preserve-aspect-ratio: 1
  preserve-natural-size: 1
  webp-lossless: 0
```
