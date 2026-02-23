# Contributing

Contributions are **welcome** and will be fully **credited**.

We accept contributions via Pull Requests on [GitHub](https://github.com/XoopsModules25x/realestate).

## Pull Requests

- **[PSR-12 Coding Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-12-extended-coding-style-guide.md)** - Check the code style with `composer cs:check` and fix it with `composer cs:fix`.
- **Add tests!** - Your patch won't be accepted if it doesn't have tests.
- **Document any change in behaviour** - Make sure the `README.md` and any other relevant documentation are kept up-to-date.
- **Consider our release cycle** - We try to follow [SemVer v2.0.0](https://semver.org/).
- **Create feature branches** - Don't ask us to pull from your master branch.
- **One pull request per feature** - If you want to do more than one thing, send multiple pull requests.
- **Send coherent history** - Make sure each individual commit in your pull request is meaningful.

## Running Quality Checks

```bash
# Run all quality checks
composer qa

# Run tests only
composer test

# Run static analysis
composer analyse

# Check code style
composer cs:check

# Fix code style
composer cs:fix
```

## Licensing

By contributing code you agree to license your contribution under the [GNU General Public License, Version 2 or any later version.](https://www.gnu.org/licenses/gpl-2.0.html)

Thank you for contributing!
