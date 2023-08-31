<?php

namespace Adrolli\FilamentJobManager\Resources;

use Adrolli\FilamentJobManager\FilamentJobsWaitingPlugin;
use Adrolli\FilamentJobManager\Models\Job;
use Adrolli\FilamentJobManager\Resources\JobsWaitingResource\Pages\ListJobs;
use Adrolli\FilamentJobManager\Resources\JobsWaitingResource\Widgets\JobStatsOverview;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class JobsWaitingResource extends Resource
{
    protected static ?string $model = Job::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('job_id')
                    ->required()
                    ->maxLength(255),
                TextInput::make('name')
                    ->maxLength(255),
                TextInput::make('queue')
                    ->maxLength(255),
                DateTimePicker::make('started_at'),
                DateTimePicker::make('finished_at'),
                Toggle::make('failed')
                    ->required(),
                TextInput::make('attempt')
                    ->required(),
                Textarea::make('exception_message')
                    ->maxLength(65535),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('status')
                    ->badge()
                    ->label(__('filament-job-manager::translations.status'))
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => __("filament-job-manager::translations.{$state}"))
                    ->color(fn (string $state): string => match ($state) {
                        'running' => 'primary',
                        'waiting' => 'success',
                        'failed' => 'danger',
                    }),
                TextColumn::make('name')
                    ->label(__('filament-job-manager::translations.name'))
                    ->sortable(),
                TextColumn::make('queue')
                    ->label(__('filament-job-manager::translations.queue'))
                    ->sortable(),
                TextColumn::make('progress')
                    ->label(__('filament-job-manager::translations.progress'))
                    ->formatStateUsing(fn (string $state) => "{$state}%")
                    ->sortable(),
                // ProgressColumn::make('progress')->label(__('filament-job-manager::translations.progress'))->color('warning'),
                TextColumn::make('started_at')
                    ->label(__('filament-job-manager::translations.started_at'))
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJobs::route('/'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            JobStatsOverview::class,
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return FilamentJobsWaitingPlugin::get()->getNavigationCountBadge() ? number_format(static::getModel()::count()) : null;
    }

    public static function getModelLabel(): string
    {
        return FilamentJobsWaitingPlugin::get()->getLabel();
    }

    public static function getPluralModelLabel(): string
    {
        return FilamentJobsWaitingPlugin::get()->getPluralLabel();
    }

    public static function getNavigationLabel(): string
    {
        return Str::title(static::getPluralModelLabel()) ?? Str::title(static::getModelLabel());
    }

    public static function getNavigationGroup(): ?string
    {
        return FilamentJobsWaitingPlugin::get()->getNavigationGroup();
    }

    public static function getNavigationSort(): ?int
    {
        return FilamentJobsWaitingPlugin::get()->getNavigationSort();
    }

    public static function getBreadcrumb(): string
    {
        return FilamentJobsWaitingPlugin::get()->getBreadcrumb();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return FilamentJobsWaitingPlugin::get()->shouldRegisterNavigation();
    }

    public static function getNavigationIcon(): string
    {
        return FilamentJobsWaitingPlugin::get()->getNavigationIcon();
    }
}
