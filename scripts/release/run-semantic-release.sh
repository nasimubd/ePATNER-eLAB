#!/usr/bin/env bash
set -euo pipefail

if [[ -z "${GH_TOKEN:-}" ]]; then
  export GH_TOKEN="$(gh auth token)"
fi

export GITHUB_TOKEN="${GITHUB_TOKEN:-$GH_TOKEN}"
export GIT_AUTHOR_NAME="${GIT_AUTHOR_NAME:-SystematicBot}"
export GIT_AUTHOR_EMAIL="${GIT_AUTHOR_EMAIL:-semantic-release-bot@martynus.net}"
export GIT_COMMITTER_NAME="${GIT_COMMITTER_NAME:-SystematicBot}"
export GIT_COMMITTER_EMAIL="${GIT_COMMITTER_EMAIL:-semantic-release-bot@martynus.net}"

npx semantic-release --no-ci "$@"
