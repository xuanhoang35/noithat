<?php
require_once __DIR__ . '/../core/Model.php';

class Slider extends Model {
    private string $storage;
    private static bool $storageEnsured = false;

    public function __construct() {
        parent::__construct();
        $this->storage = __DIR__ . '/../config/slider.json';
        $this->ensureStorage();
    }

    private function ensureStorage(): void {
        if (self::$storageEnsured) return;
        if (!file_exists($this->storage)) {
            @file_put_contents($this->storage, json_encode([]));
        }
        self::$storageEnsured = true;
    }

    private function load(): array {
        $this->ensureStorage();
        $json = @file_get_contents($this->storage);
        $data = json_decode($json, true);
        return is_array($data) ? $data : [];
    }

    private function save(array $items): void {
        $this->ensureStorage();
        file_put_contents($this->storage, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    public function all(bool $onlyActive = true): array {
        $items = $this->load();
        if ($onlyActive) {
            $items = array_filter($items, function($row){ return (int)($row['is_active'] ?? 1) === 1; });
        }
        usort($items, function($a, $b){
            return strcmp($b['created_at'] ?? '', $a['created_at'] ?? '');
        });
        return array_values($items);
    }

    public function create(string $path): int {
        $items = $this->load();
        $nextId = 1;
        foreach ($items as $row) {
            $nextId = max($nextId, (int)$row['id'] + 1);
        }
        $items[] = [
            'id' => $nextId,
            'image' => $path,
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->save($items);
        return $nextId;
    }

    public function delete(int $id): void {
        $items = $this->load();
        $remaining = [];
        $deleted = null;
        foreach ($items as $row) {
            if ((int)$row['id'] === (int)$id) {
                $deleted = $row;
                continue;
            }
            $remaining[] = $row;
        }
        $this->save($remaining);
        if ($deleted && !empty($deleted['image'])) {
            $this->deleteLocalFile($deleted['image']);
        }
    }

    private function deleteLocalFile(string $path): void {
        $path = trim($path);
        if ($path === '' || preg_match('#^(?:https?:)?//#', $path)) {
            return;
        }
        $full = realpath(__DIR__ . '/../' . ltrim($path, '/'));
        if ($full && file_exists($full) && is_file($full)) {
            @unlink($full);
        }
    }
}
