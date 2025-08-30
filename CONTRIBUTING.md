# Contributing to HookPress

Thanks for taking the time to contribute! This guide explains how to set up your environment, the coding standards we use, how to run the test suite, and what we expect in pull requests.

HookPress is a Laravel package that builds a cached class map at install/update time using Composer hooks. Please keep contributions focused, well-tested, and easy to review.

---

## Table of contents

- [Project setup](#project-setup)
- [Running tests](#running-tests)
- [Code style](#code-style)
- [Design notes specific to HookPress](#design-notes-specific-to-hookpress)
- [Adding or changing built-in conditions](#adding-or-changing-built-in-conditions)
- [Documentation updates](#documentation-updates)
- [Commit style & branches](#commit-style--branches)
- [Pull request checklist](#pull-request-checklist)

---

## Project setup

1. **Fork** the repository and **clone** your fork.
2. Make sure you have **PHP 8.1+** and **Composer** installed.
3. Install dependencies:

   ```bash
   composer install
   ```

4. Ensure you can run the test suite:

   ```bash
   composer test
   ```

No database or `.env` is required; this is a package repository.

---

## Running tests

We use **Pest** with **Orchestra Testbench**.

```bash
# whole suite
composer test

# or directly
./vendor/bin/pest -d
```

> Tests must pass locally before you open a PR. If you add new functionality, please add tests that cover it (unit + feature where appropriate).

---

## Code style

We use **Laravel Pint** (PSR-12 + Laravel presets).

```bash
# check
./vendor/bin/pint --test

# fix
./vendor/bin/pint
```

Commit only formatted code. Our CI will fail on style errors.

---

## Design notes specific to HookPress

- **Do not** write to `vendor/composer/autoload_classmap.php` in tests or runtime.
- The scanner reads a configurable classmap path: `config('hookpress.composer.classmap_path')`.
  - Tests should point this to a **temporary file** under `tests/.tmp/autoload_classmap.php`.
- Discovery should be **build-time only**. At runtime we read a **single cached array** (file or cache store).
- Be mindful of performance: avoid unnecessary reflection and disk I/O.
- Keep the public API stable:
  - Facade methods: `map()`, `map('type')`, `classesUsing($traitFqcn)`, `refresh()`, `clear()`.
  - Console: `hookpress:build`, `hookpress:show`, `hookpress:clear`.

---

## Adding or changing built-in conditions

Built-ins live under `src/Conditions/*` and implement `HookPress\Contracts\Condition`.

1. Add your condition class (e.g., `HasConstructorParam`).
2. Register it in `HookPressServiceProvider` under the `$builtIns` array with a clear key.
3. Add **unit tests** for:
   - positive case(s)
   - negative case(s)
   - invalid / edge arguments
4. Add **feature tests** if behavior touches the mapper flow.
5. Update the **README** “Available built-in conditions” table.

> You can also support custom conditions without modifying the core: reference a condition **FQCN** in the config. Still add tests + docs if you’re proposing new public behavior.

---

## Documentation updates

If your change affects public behavior or options, please update:
- `README.md` (usage, examples, conditions table)
- Any relevant code comments
- CHANGELOG entry (maintainers may adjust on release)

---

## Commit style & branches

We recommend **Conventional Commits**:

- `feat: add NameMatches condition`
- `fix: avoid duplicate requires in Mapper`
- `docs: expand README examples`
- `test: cover exclusions for regex`
- `refactor: simplify ConditionEvaluator resolve logic`
- `chore: bump testbench`

Use topic branches:
- `feat/condition-name-matches`
- `fix/repo-cache-ttl`
- `docs/readme-conditions-table`

---

## Pull request checklist

Before you submit:

- [ ] Tests added/updated and **passing** locally (`composer test`).
- [ ] Code formatted (`./vendor/bin/pint`).
- [ ] No writes to Composer’s real classmap; tests use `hookpress.composer.classmap_path`.
- [ ] Public API unchanged (or documented and discussed).
- [ ] README updated (usage, examples, conditions table) if needed.
- [ ] No unrelated changes bundled in the PR.

Thank you! 🙌
