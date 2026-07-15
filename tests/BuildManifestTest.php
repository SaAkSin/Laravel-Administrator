<?php

use PHPUnit\Framework\TestCase;

class BuildManifestTest extends TestCase
{
	private array $manifest;

	protected function setUp(): void
	{
		$manifestPath = __DIR__ . '/../public/dist/.vite/manifest.json';

		$this->assertFileExists($manifestPath, 'Vite manifest가 존재해야 합니다.');

		$manifest = json_decode((string) file_get_contents($manifestPath), true);

		$this->assertIsArray($manifest, 'Vite manifest는 유효한 JSON 객체여야 합니다.');
		$this->manifest = $manifest;
	}

	public function testAppEntryAndReferencedAssetsExist(): void
	{
		$entry = $this->manifest['resources/js/app.ts'] ?? null;

		$this->assertIsArray($entry);
		$this->assertTrue($entry['isEntry'] ?? false);
		$this->assertBuildFileExists($entry['file'] ?? null);

		foreach ($entry['css'] ?? [] as $cssFile) {
			$this->assertBuildFileExists($cssFile);
		}
	}

	public function testSilverThemeEntryAndAssetExist(): void
	{
		$entry = $this->manifest['resources/css/themes/silver.css'] ?? null;

		$this->assertIsArray($entry);
		$this->assertTrue($entry['isEntry'] ?? false);
		$this->assertBuildFileExists($entry['file'] ?? null);
	}

	private function assertBuildFileExists(?string $relativePath): void
	{
		$this->assertNotEmpty($relativePath, 'manifest 엔트리에 산출물 경로가 있어야 합니다.');
		$this->assertFileExists(__DIR__ . '/../public/dist/' . $relativePath);
	}
}
