import { readFile, writeFile } from "node:fs/promises";

const version = process.argv[2];

if (!/^\d+\.\d+\.\d+(?:-[0-9A-Za-z.-]+)?(?:\+[0-9A-Za-z.-]+)?$/.test(version ?? "")) {
  throw new Error("Expected a semantic version argument.");
}

const readme = await readFile("README.md", "utf8");
const updated = readme.replace(
  /https:\/\/img\.shields\.io\/badge\/version-[^-?]+-[^?]+\.svg\?logo=git&logoColor=white/,
  `https://img.shields.io/badge/version-${version}-blue.svg?logo=git&logoColor=white`,
);

if (updated === readme) {
  throw new Error("README version badge was not found.");
}

await writeFile("README.md", updated);
