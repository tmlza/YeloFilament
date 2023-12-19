<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Post;
use Filament\Tables;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ColorPicker;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Forms\Components\MarkdownEditor;
use App\Filament\Resources\PostResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Filament\Resources\PostResource\RelationManagers\AuthorsRelationManager;
use App\Models\User;
use Filament\Tables\Filters\SelectFilter;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')->required(),
                TextInput::make('slug')->required(),
                Select::make('category_id')
                ->label('Category')
                ->options(Category::all()->pluck('name','id')),
                MarkdownEditor::make('content')->nullable()
                ->columnSpanFull(),
                FileUpload::make('thumbnail')->disk('public')->directory('thumbnails'),
                FileUpload::make('thumbnail')
                ->preserveFilenames()
                ->directory('thumbnails')
                ->getUploadedFileNameForStorageUsing(function(TemporaryUploadedFile $file):string{
                    return (string) str($file->getClientOriginalName())->prepend(now()->timestamp);
                }),
                ColorPicker::make('color')->required(),
                TagsInput::make('tags')->nullable(),
                Checkbox::make('published')->required(),
                Section::make('Authors')->schema([
                    Select::make('authors')
                    ->label('Co Authors')
                    ->preload()
                    // ->multiple()
                    // ->searchable()
                    // ->native('false')
                    // ->options(User::all()->pluck('name','id')),
                    ->relationship('authors','name')
                    ->multiple()
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('slug'),
                TextColumn::make('Category.name'),
                ColorColumn::make('color'),
                ImageColumn::make('thumbnail'),
                TextColumn::make('tags'),
                CheckboxColumn::make('published'),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AuthorsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
