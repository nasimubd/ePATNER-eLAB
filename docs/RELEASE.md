# Release workflow

PulseForge releases are run manually from a clean local checkout with semantic-release. Continuous integration is intentionally skipped for release operations; the release commit is marked `[skip ci]` and semantic-release runs with `--no-ci`.

## Version rules

Conventional Commit history determines the version bump:

| Commit type | Bump |
| --- | --- |
| `feat` | Minor |
| `fix`, `docs`, `refactor`, `perf`, `test`, `build`, `ci`, `chore` | Patch |
| `type!` or a `BREAKING CHANGE:` footer | Major |
| `chore(release)`, `chore(deps)`, `chore(ci)` | No release |

## Initial release

The initial public baseline is `v0.1.0`, matching the README. Create it once from the reviewed `main` head with the release commit format `chore(release): 0.1.0 [skip ci]`, then publish the matching GitHub Release. Later releases use semantic-release.

## Later releases

```bash
npm ci
npm run release:doctor
npm run release:dry
npm run release
```

`release:dry` previews the computed version and notes without writing or publishing. `release` generates `CHANGELOG.md`, updates the README version badge, creates a `chore(release): X.Y.Z [skip ci]` commit, tags it as `vX.Y.Z`, and creates the GitHub Release.

The release commit uses the `SystematicBot` Git identity. GitHub Release ownership is determined by the GitHub token used to publish it.
