# Contributing to PulseForge

## Conventional Commits doctrine

PulseForge uses the [Conventional Commits](https://www.conventionalcommits.org/) specification for every commit and pull-request title. This creates a consistent, machine-readable history for changelogs, release automation, and semantic versioning.

### Required format

```text
<type>(<optional scope>): <short imperative description>
```

Use a lowercase description with no trailing period. Keep the subject concise; explain implementation and context in the commit body or pull request.

### Allowed types

| Type | Use for |
| --- | --- |
| `feat` | A new user-facing capability. |
| `fix` | A bug fix, including security fixes. |
| `docs` | Documentation-only changes. |
| `refactor` | Code changes that neither add a feature nor fix a bug. |
| `perf` | A measurable performance improvement. |
| `test` | Adding or correcting tests. |
| `build` | Build system or dependency changes. |
| `ci` | Continuous-integration configuration or automation. |
| `chore` | Maintenance that does not affect application behaviour. |
| `revert` | Reverting an earlier commit. |

Use an optional scope when it makes the change easier to identify. Common scopes include `auth`, `patients`, `appointments`, `lab`, `billing`, `ledger`, `bookings`, `admin`, `docs`, `deps`, `ci`, and `security`.

### Examples

```text
feat(lab): add reusable report templates
fix(bookings): prevent overlapping OT reservations
fix(security): remove tracked development database
docs: establish Conventional Commits doctrine
refactor(billing): extract invoice total calculator
build(deps): upgrade Laravel framework
```

### Breaking changes

Mark a breaking change with `!` after the type or scope, and explain it in a `BREAKING CHANGE:` footer.

```text
feat(api)!: replace legacy appointment endpoint

BREAKING CHANGE: clients must use /api/v2/appointments.
```

## Pull requests

Every pull-request title must use the same Conventional Commits format as its commits. A pull request should be focused, include validation notes, and describe any user-visible or operational impact.

Do not merge a pull request whose title does not follow this doctrine. If the change fixes a security issue, use `fix(security): ...` and avoid exposing sensitive details in public discussion.

## Development workflow

1. Create a focused branch from `main`.
2. Make small, reviewable commits using the required format.
3. Run the relevant tests and build checks.
4. Open a pull request using the required title format and template.
5. Address review feedback with additional conventional commits.
