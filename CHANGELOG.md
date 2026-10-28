# Changelog
All notable changes to this project will be documented in this file.

## [Unreleased]
- Laravel 12 and 13 support added.
- PHPUnit 12 support for tests.
- PHPUnit XML configuration update.
- Fixed PHP 8.4 implicit nullable parameter deprecations in `SeederValidator` and `CommandExceptionHandler`.
- Code style formatted to PSR-12 with Laravel Pint (`composer format` / `composer lint`).
- Replaced Travis CI with GitHub Actions (PHP 8.2–8.4 × Laravel 11/12/13, plus a code style check).
- Fixed the test suite running zero tests on PHPUnit 9 (Laravel 8/9): test methods now carry `/** @test */` docblocks alongside `#[Test]`.
- Renamed the PHPUnit test suite from "Laravel Model Hashid TestSuite" (copy-paste) to "Laravel Seed Extender TestSuite".
- Re-enabled the `it_will_geneate_seeder_without_specifying_table_name` command test; its fixture already matches the generated seeder.
- **Behaviour change:** fixed `Seeder::withModelEvents()` being inverted. `withModelEvents(true)` used to seed quietly (no model events) and `withModelEvents(false)` fired them. Now `withModelEvents(true)` fires model events and `withModelEvents(false)` seeds quietly. `withModelEvents()` with no argument still fires them, and seeding without calling it is still quiet.
- Fixed the facade alias: the package now registers `SeedExtender`. The old copy-pasted `ModelRepository` alias is kept for backward compatibility and will be removed in the next major release.
- `make:extend-seeder` now returns an explicit exit code (`Command::SUCCESS` / `Command::FAILURE`) from `handle()` instead of `null` on success.


## [1.0.0] - 2021-09-12
- Initial release

## [1.0.1] - 2021-09-15
- Test update

## [1.0.2] - 2021-09-15
- Test update

## [1.0.3] - 2021-11-15
- Removed **$column** property from generated seeder class.
- Added the columns list handling process more dynamic.
- Feature optimization. 
- README update.

## [1.1.0] - 2021-12-02
- Added functionality to handle seeding via table model class.
- Code Optimization.
- Existing feature optimization. 
- Tests update.
- Bug Fix.
- README update.

## [1.1.1] - 2022-03-18
- Bug Fix.
- Tests update.
- README update.

## [1.1.2] - 2023-05-27
- Laravl 10 support added

## [1.2.0] - 2024-08-17
- Laravl 11 support added
- PHPUnit upgraded to 11.3
- Tests update
