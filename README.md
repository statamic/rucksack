# Rucksack ![Statamic 2.0](https://img.shields.io/badge/statamic-2.0-blue.svg?style=flat-square)
> Allow your users to add content and assets to their session. Like a cart for content!

## Usage example
Let's say you have a portfolio of projects which contains images. You want to allow your
users to pick their favorite images. With Rucksack, you can let your users add the
image assets to their "rucksack" (or "cart").

## Add to rucksack
```
<a href="
{{ rucksack:add_url
   id="123"                  # The ID of the content you're adding. Leave this off to get id from context.
   extra:foo="bar"           # Any extra data you want to be made available as single tags.
   extra:project_id="456"    # If it ends with _id, it will be converted to the content.
}}
">Add to rucksack</a>

Outputs:
<a href="/!/Rucksack/add/123?foo=bar&project_id=456">Add to rucksack</a>
```

## Removing an item from your rucksack
```
<a href="{{ rucksack:remove_url id="123" }}">Remove</a>

Outputs:
<a href="/!/Rucksack/remove/123">Remove</a>
```

## Checking if an item is in your rucksack
```
{{ if {rucksack:has id="123"} }}
    You have this!
{{ else }}
    You don't have this!
{{ /if }}
```

## Outputting rucksack contents
```
{{ rucksack:contents as="items" }}

    {{ if no_results }}

        Rucksack is empty!

    {{ else }}

        <div class="rucksack-items">

            {{ items }}
                <!-- Variables from the item you added are available -->
                {{ title }}, {{ url }}, etc

                <!-- "Extra" variables you specified when adding to the rucksack are available -->
                {{ foo }}

                <!-- "Extra" variables that were suffixed by _id are converted to their content,
                     and available in an array -->
                {{ project }}
                    {{ title }}, {{ url }}, etc
                {{ /project }}

                <!-- Of course, they're available using a colon syntax -->
                {{ project:title }}, {{ project:url }}, etc

                <!-- Remove items like this. The ID will be resolved from the context. -->
                <a href="{{ rucksack:remove_url }}">Remove</a>
            {{ /items }}

        </div>

    {{ /if }}
{{ /rucksack:contents }}
```

## External Access

When you add something to your rucksack, since it's managed in session, it's only available to you.
To facilitate sharing of a rucksack's contents, you may generate a hash and reference that
when outputting contents.

```
/wishlist?hash={{ rucksack:external_hash }}
```

This will save the rucksack's contents to cache and outputs a corresponding hash. Something like:

```
/wishlist?hash=f8d9aoh4389fdsf
```

Then in your `{{ rucksack:contents }}` tag, passing along the hash will output the contents for _that_ rucksack:

```
{{ rucksack:contents hash="{get:hash}" }}
    ...
{{ /rucksack:contents }}
```

Note that the hashed value will only correspond to the contents for when it was generated. Any updates to the
rucksack will not be reflected unless a new hash is generated.
