# KeepExp

Keep player experience after death in selected Axolotl-PM worlds

[![CI](https://github.com/NhanAZ-Plugins/KeepExp/actions/workflows/build.yml/badge.svg)](https://github.com/NhanAZ-Plugins/KeepExp/actions/workflows/build.yml)
[![Latest release](https://img.shields.io/github/v/release/NhanAZ-Plugins/KeepExp)](https://github.com/NhanAZ-Plugins/KeepExp/releases/latest)
[![License](https://img.shields.io/github/license/NhanAZ-Plugins/KeepExp)](https://github.com/NhanAZ-Plugins/KeepExp/blob/master/LICENSE)
[![Discord](https://img.shields.io/discord/986553214889517088?label=Discord&logo=discord&logoColor=white&color=5865F2)](https://discord.gg/j2X83ujT6c)

## Overview

This plugin targets the PocketMine-MP and Axolotl-PM API 5 ecosystem and is maintained under [NhanAZ-Plugins](https://github.com/NhanAZ-Plugins).

## Requirements and compatibility

- Axolotl-PM with plugin API `5.0.0`.
- PHP 8.1 or newer in the Axolotl-PM 5 toolchain.

The [build workflow](.github/workflows/build.yml) pins the Axolotl-PM source used for PHPStan at maximum level. A specific server version tested with the PHAR is recorded in release evidence. Compatibility with every Axolotl-PM 5.x release has not been established.

## Installation

Download `KeepExp.phar` from a verified [GitHub release](https://github.com/NhanAZ-Plugins/KeepExp/releases), place it in the server's `plugins/` directory and restart the server. The plugin has no commands, permissions or external plugin dependencies.

## Configuration

Edit `plugin_data/KeepExp/config.yml` after the first start, then restart the server to apply changes.

| Setting | Default | Behavior |
|---|---|---|
| `keepExp` | `true` | Enable experience protection in selected worlds. |
| `typeExp` | `realExp` | `realExp` keeps all current XP. `droppedExp` keeps only the amount that would normally drop on death. |
| `mode` | `all` | `all`, `whitelist` or `blacklist` determines which worlds are protected. |
| `worlds` | `[]` | World display names used by whitelist and blacklist modes. |
| `msgType` | `none` | Optional respawn message through chat, title, popup, tip, action bar or toast. |
| `msgAfterRespawn` | `You died, but your experience is safe!` | Respawn message text. |
| `toastTitle` | `KeepExp` | Title used when `msgType` is `toast`. |

The server's native keep-XP event flag protects experience even when a player disconnects before respawning. In `droppedExp` mode, the plugin changes the player's current XP to the would-be drop amount without increasing their lifetime XP counter. Invalid setting types or choices generate warnings and use documented defaults.

## Building and testing

The [build workflow](.github/workflows/build.yml) tests world selection, runs PHPStan at maximum level, builds and inspects the PHAR with [DevTools](https://github.com/NhanAZ/DevTools), and uploads the verified artifact. A clean Axolotl-PM startup confirms the PHAR loads. Player death, respawn messaging and the Bedrock client display require an in-game test.

## License and support

KeepExp is licensed under [GPL-3.0](LICENSE). For help, use [NhanAZ Discord](https://discord.gg/j2X83ujT6c).
