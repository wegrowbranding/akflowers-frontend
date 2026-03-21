<?php

class View {
    public static function render(string $view, array $data = []): void {
        extract($data);
        $viewFile = APP . '/Views/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewFile)) {
            throw new RuntimeException("View not found: {$viewFile}");
        }
        require $viewFile;
    }
}
