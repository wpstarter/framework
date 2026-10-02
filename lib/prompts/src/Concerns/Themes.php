<?php

namespace WpStarter\Prompts\Concerns;

use InvalidArgumentException;
use WpStarter\Prompts\AutoCompletePrompt;
use WpStarter\Prompts\Callout;
use WpStarter\Prompts\Clear;
use WpStarter\Prompts\ConfirmPrompt;
use WpStarter\Prompts\DataTablePrompt;
use WpStarter\Prompts\Grid;
use WpStarter\Prompts\MultiSearchPrompt;
use WpStarter\Prompts\MultiSelectPrompt;
use WpStarter\Prompts\Note;
use WpStarter\Prompts\NumberPrompt;
use WpStarter\Prompts\PasswordPrompt;
use WpStarter\Prompts\PausePrompt;
use WpStarter\Prompts\Progress;
use WpStarter\Prompts\Prompt;
use WpStarter\Prompts\SearchPrompt;
use WpStarter\Prompts\SelectPrompt;
use WpStarter\Prompts\Spinner;
use WpStarter\Prompts\Stream;
use WpStarter\Prompts\SuggestPrompt;
use WpStarter\Prompts\Table;
use WpStarter\Prompts\Task;
use WpStarter\Prompts\TextareaPrompt;
use WpStarter\Prompts\TextPrompt;
use WpStarter\Prompts\Themes\Default\AutoCompletePromptRenderer;
use WpStarter\Prompts\Themes\Default\CalloutRenderer;
use WpStarter\Prompts\Themes\Default\ClearRenderer;
use WpStarter\Prompts\Themes\Default\ConfirmPromptRenderer;
use WpStarter\Prompts\Themes\Default\DataTableRenderer;
use WpStarter\Prompts\Themes\Default\GridRenderer;
use WpStarter\Prompts\Themes\Default\MultiSearchPromptRenderer;
use WpStarter\Prompts\Themes\Default\MultiSelectPromptRenderer;
use WpStarter\Prompts\Themes\Default\NoteRenderer;
use WpStarter\Prompts\Themes\Default\NumberPromptRenderer;
use WpStarter\Prompts\Themes\Default\PasswordPromptRenderer;
use WpStarter\Prompts\Themes\Default\PausePromptRenderer;
use WpStarter\Prompts\Themes\Default\ProgressRenderer;
use WpStarter\Prompts\Themes\Default\SearchPromptRenderer;
use WpStarter\Prompts\Themes\Default\SelectPromptRenderer;
use WpStarter\Prompts\Themes\Default\SpinnerRenderer;
use WpStarter\Prompts\Themes\Default\StreamRenderer;
use WpStarter\Prompts\Themes\Default\SuggestPromptRenderer;
use WpStarter\Prompts\Themes\Default\TableRenderer;
use WpStarter\Prompts\Themes\Default\TaskRenderer;
use WpStarter\Prompts\Themes\Default\TextareaPromptRenderer;
use WpStarter\Prompts\Themes\Default\TextPromptRenderer;
use WpStarter\Prompts\Themes\Default\TitleRenderer;
use WpStarter\Prompts\Title;

trait Themes
{
    /**
     * The name of the active theme.
     */
    protected static string $theme = 'default';

    /**
     * The available themes.
     *
     * @var array<string, array<class-string<Prompt>, class-string<object&callable>>>
     */
    protected static array $themes = [
        'default' => [
            TextPrompt::class => TextPromptRenderer::class,
            NumberPrompt::class => NumberPromptRenderer::class,
            TextareaPrompt::class => TextareaPromptRenderer::class,
            PasswordPrompt::class => PasswordPromptRenderer::class,
            SelectPrompt::class => SelectPromptRenderer::class,
            MultiSelectPrompt::class => MultiSelectPromptRenderer::class,
            ConfirmPrompt::class => ConfirmPromptRenderer::class,
            PausePrompt::class => PausePromptRenderer::class,
            SearchPrompt::class => SearchPromptRenderer::class,
            MultiSearchPrompt::class => MultiSearchPromptRenderer::class,
            SuggestPrompt::class => SuggestPromptRenderer::class,
            Spinner::class => SpinnerRenderer::class,
            Note::class => NoteRenderer::class,
            Table::class => TableRenderer::class,
            Progress::class => ProgressRenderer::class,
            Clear::class => ClearRenderer::class,
            Grid::class => GridRenderer::class,
            AutoCompletePrompt::class => AutoCompletePromptRenderer::class,
            Title::class => TitleRenderer::class,
            Stream::class => StreamRenderer::class,
            Task::class => TaskRenderer::class,
            DataTablePrompt::class => DataTableRenderer::class,
            Callout::class => CalloutRenderer::class,
        ],
    ];

    /**
     * Get or set the active theme.
     *
     * @throws InvalidArgumentException
     */
    public static function theme(?string $name = null): string
    {
        if ($name === null) {
            return static::$theme;
        }

        if (! isset(static::$themes[$name])) {
            throw new InvalidArgumentException("Prompt theme [{$name}] not found.");
        }

        return static::$theme = $name;
    }

    /**
     * Add a new theme.
     *
     * @param  array<class-string<Prompt>, class-string<object&callable>>  $renderers
     */
    public static function addTheme(string $name, array $renderers): void
    {
        if ($name === 'default') {
            throw new InvalidArgumentException('The default theme cannot be overridden.');
        }

        static::$themes[$name] = $renderers;
    }

    /**
     * Get the renderer for the current prompt.
     */
    protected function getRenderer(): callable
    {
        $class = get_class($this);

        return new (static::$themes[static::$theme][$class] ?? static::$themes['default'][$class])($this);
    }

    /**
     * Render the prompt using the active theme.
     */
    protected function renderTheme(): string
    {
        $renderer = $this->getRenderer();

        return $renderer($this);
    }
}
