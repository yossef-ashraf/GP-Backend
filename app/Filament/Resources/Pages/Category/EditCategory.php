<?php

namespace App\Filament\Resources\Pages\Category;

use App\Filament\Resources\CategoryResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->modalHeading('Delete Category')
                ->modalDescription(function ($record) {
                    $childrenCount = $record->children()->count();
                    if ($childrenCount > 0) {
                        return "This category has {$childrenCount} child categories. What would you like to do with them?";
                    }
                    return 'Are you sure you want to delete this category?';
                })
                ->modalSubmitActionLabel('Delete Category')
                ->before(function (DeleteAction $action, $record) {
                    // Handle children - set their parent_id to null (make them root categories)
                    if ($record->children()->exists()) {
                        $record->children()->update(['parent_id' => null]);
                        
                        Notification::make()
                            ->title('Children Categories Updated')
                            ->body('Child categories have been moved to root level.')
                            ->success()
                            ->send();
                    }
                }),
        
        ];
    }
}