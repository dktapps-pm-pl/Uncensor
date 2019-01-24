# Uncensor

A plugin for PocketMine-MP to automatically bypass client-sided profanity filtering.

## How to use

- Drop the plugin into your plugins folder
- Acquire the game's current `profanity_filter.wlist` and place it in the plugin's data folder
- Profit!

## Where do I get `profanity_filter.wlist` from?

You can extract it from any current version of the game. It is not included with the files for this plugin due to potential licensing issues.

## How does it work?

Very simple. It simply inserts an ASCII file-separator character (ASCII code 28, 0x1c) after the first letter of the filtered word. This ensures that it does not match client-sided filtering.

## Will it interfere with any swear-filter plugins I already have?

This plugin operates directly on TextPacket as it is sent. Most swear-filter plugins will already have redacted bad words by the time the message reaches this plugin. It is therefore unlikely to interfere with existing plugins, however this has not been tested.