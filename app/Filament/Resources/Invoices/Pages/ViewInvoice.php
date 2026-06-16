<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use App\Mail\InvoiceMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Mail;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('markAsPaid')
                ->label('Mark as Paid')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn() => $this->record->status !== 'paid')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update([
                        'status' => 'paid'
                    ]);

                    Notification::make()
                        ->title('Invoice marked as paid')
                        ->success()
                        ->send();
                }),

            Action::make('downloadPdf')
                ->label('Download PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->url(fn() => route('invoice.pdf', $this->record))
                ->openUrlInNewTab(),

            Action::make('sendEmail')
                ->label('Send Email')
                ->icon('heroicon-o-envelope')
                ->color('info')
                ->requiresConfirmation()
                ->action(function () {

                    $invoice = $this->record;

                    $invoice->load([
                        'client',
                        'items.product'
                    ]);

                    $pdf = Pdf::loadView(
                        'pdf.invoice',
                        compact('invoice')
                    );

                    $path = storage_path(
                        "app/invoices/invoice-{$invoice->id}.pdf"
                    );

                    if (! file_exists(dirname($path))) {
                        mkdir(dirname($path), 0755, true);
                    }

                    $pdf->save($path);

                    Mail::to($invoice->client->email)
                        ->send(
                            new InvoiceMail(
                                $invoice,
                                $path
                            )
                        );

                    Notification::make()
                        ->title('Invoice emailed successfully')
                        ->success()
                        ->send();
                }),

            EditAction::make(),
            DeleteAction::make(),
            RestoreAction::make(),
            ForceDeleteAction::make()
        ];
    }
}
