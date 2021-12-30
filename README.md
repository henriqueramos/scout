
# Scout
A CLI tool for OJS/OMP/OPS 3.4.0+ to help you to manage your application.

## Usage
1. Download the latest version of `scout.phar` from the [Releases page](//github.com/henriqueramos/scout/releases)
2. Add it into your OJS/OMP/OPS root folder
3. Run from your terminal `php scout.phar`
4. Enjoy!
## Default available commands
Currently we have this default available commands
| Command | Description |
|--|--|
| help | Display help for a command |
| list | List all commands |
| inspire | Display an inspiring quote every time |
| maintenance | Create the `.maintenance` file on root folder application.To be use with [maintenanceMode plugin](https://github.com/henriqueramos/maintenanceMode) |
| roles:list | List all roles for a certain context |
| users:add | Add an User on a certain context |
| users:count_by_role | Count users of a certain context by role |
| users:disable | Disable a list of users Ids |
| users:enable | Enable a list of users Ids |
| users:lists | List all users of a certain context |


## How this became alive?
During the 2021's holidays, I had an idea to create a tool like Laravel's `artisan` for PKP ecosystem. And after a few coding days, `scout` was born.

The tool's name came from the military term for [perform reconnaissance](https://en.wikipedia.org/wiki/Reconnaissance).

## Can I build this?

Of course, you will need to use the `box-project/box` utility tool to create a `phar` file from this repository.

## How I can help?
Pull Requests are welcome!