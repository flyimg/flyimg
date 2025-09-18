# Application Options

## The file config/parameters.yml

Here are the app options you can configure with the [config/parameters.yml](https://github.com/flyimg/flyimg/blob/main/config/parameters.yml) these options operate at runtime, you don't need to rebuild the container or restart any service, all requests<sup><a name="footnote1">1</a></sup> will check this config.

### debug

_Defaults to:_ `false`
_Description:_ Enables debug mode, currently is used only for the tests, so there's no harm in leaving it as it is.

### log_level

_Defaults to:_ `error`
_Description:_ Log level, possible values are: debug, info, notice, warning, error

### app_domain

_Defaults to:_ ``
_Description:_ Custom domain name (optional) - used when running in containers to override localhost detectionm, leave empty to use automatic detection from HTTP headers

### home_page_title

_Defaults to:_ `Flyimg`
_Description:_ Home Page title

### demo_page_enabled

_Defaults to:_ `true`
_Description:_ Enable or disable the demo page (default /). When false, returns an empty page with 200 code

### enable_cronjob_cleanup

_Defaults to:_ `true`
_Description:_ # To enable the Cleanup Cronjob to purge the var/tmp folder

### cronjob_cleanup_interval

_Defaults to:_ `0 */5 * * *`
_Description:_ The cronjob interval to cleanup the var/tmp folder

### enable_avif

_Defaults to:_ `true`
_Description:_ Serve AVIF automatically to Browsers supporting it. You can always request an image in Avif format passing the `o_avif` [URL option key](https://github.com/flyimg/flyimg/blob/main/docs/url-options.md).

### enable_webp

_Defaults to:_ `true`
_Description:_ Serve WebP automatically to Browsers supporting it. You can always request an image in webP format passing the `o_webp` [URL option key](https://github.com/flyimg/flyimg/blob/main/docs/url-options.md).

### header_cache_days

_Defaults to:_ `365`
_Description:_ Number of days for header cache expires `max_age`, this is the header sent to the client or browser requesting the resource. You can pass cache busting parameters to the URL which will break cache in all modern proxies and Browsers.

### options_separator

_Defaults to:_ `,`
_Description:_ URL options are separated by default by comas `,` but you can change that to some other character, like `._~:[]@!$'()*+;` just be carefull that it doesn't conflict with the sintaz of options you are passing to the URL, there is no strict checking of separating characters.

!!! Important
    When changing `options_separator` in `config/parameters`, you need to change the `OPTIONS_SEPARATOR` value in `web/js/main.js`.

### restricted_domains

_Defaults to:_ `false`
_Description:_ This restricts fetching images for transformations only from _whitelisted domains_ (see `whitelist_domains`). A good measure of safety and to prevent abuse of your app from third parties is to set `restricted_domains` to `true`, this way the app will download and try to transform resources only from domains you trust or have control of.

### whitelist_domains

_Defaults to:_

```yml
    - domain-1.com
    - domain-2.com
```

_Description:_ If `restricted_domains` is enabled, put your whitelisted domains in this list. You can use both exact domain matches and wildcard patterns:

- **Exact matches**: `example.com` - matches only `example.com`
- **Wildcard patterns**: `*.example.com` - matches any subdomain of `example.com` (e.g., `api.example.com`, `cdn.example.com`, `www.example.com`)

Example configuration:
```yml
whitelist_domains:
  - example.com
  - "*.example.org"
  - api.myservice.com
  - "*.cdn.myservice.com"
```

**Note**: Wildcard patterns must be quoted in YAML to avoid parsing errors, as the `*` character has special meaning in YAML.

For the [Digital Ocean Provisioning Script](https://github.com/flyimg/DigitalOcean-provision) you can set the restricted domains at the droplet provisioning step.

### disable_cache

_Defaults to:_ `false`
_Description:_ When set to true the generated image will be deleted from the cache in web/upload and served directly in the response


### storage_system

_Defaults to:_ `local`

More info at [Storage Options](storage-options.md).


### aws_s3

When using S3 sources (e.g., `s3://bucket/key`), configure the S3 parameters in `config/parameters.yml` under `aws_s3`:

```yml
aws_s3:
  access_id: "..."
  secret_key: "..."
  region: "eu-central-1"
  bucket_name: "my-bucket"
  # path_prefix: ''
  # visibility: 'PRIVATE'   # PUBLIC or PRIVATE
  # endpoint: 'https://%s.s3.%s.amazonaws.com/'
  # bucket_endpoint: false
  # use_path_style_endpoint: false
```

- `endpoint` can target third-party S3-compatible services. It should accept `%s` placeholders for bucket and region.
- For private objects, you may need to forward request headers (e.g., `Authorization`).

### forward_request_headers

List of request headers that Flyimg will forward when fetching the source image. Useful when the upstream requires authentication.

```yml
forward_request_headers:
  - Authorization
```

### header_extra_options

Additional HTTP headers sent when fetching source images (useful for custom User-Agent, etc.).

```yml
header_extra_options:
  - 'User-Agent: Mozilla/5.0 (... your UA ... )'
```

### POST upload endpoint

Flyimg supports uploading images via POST to avoid long URLs:

- Endpoint: `POST /upload/{image_options}`
- Bodies: raw binary (`application/octet-stream` or `image/*`) or JSON `{"base64":"..."}` / `{"dataUri":"data:..."}`

See examples in [URL Options](url-options.md#alternative-sources-and-post-uploads).
