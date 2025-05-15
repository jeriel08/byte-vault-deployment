<?php

namespace App\Exports;

use App\Models\Product; // Make sure your Product model namespace is correct
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;      // <-- Add this
use Maatwebsite\Excel\Concerns\WithColumnFormatting; // <-- Add this
use Maatwebsite\Excel\Concerns\WithStyles;           // <-- Add this
use Maatwebsite\Excel\Concerns\WithEvents;          // <-- Add this
use Maatwebsite\Excel\Events\AfterSheet;           // <-- Add this
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;      // <-- Add this for styling
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;     // <-- Add this for number formats
use PhpOffice\PhpSpreadsheet\Style\Alignment;       // <-- Add this for alignment


class InventoryReportExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithColumnWidths,
    WithColumnFormatting,
    WithStyles,
    WithEvents
{
    protected $salesOrdersCount;
    protected $salesTotalValue;
    protected $dateRangeDisplay;

    public function __construct(int $salesOrdersCount, float $salesTotalValue, string $dateRangeDisplay)
    {
        $this->salesOrdersCount = $salesOrdersCount;
        $this->salesTotalValue = $salesTotalValue;
        $this->dateRangeDisplay = $dateRangeDisplay;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        return Product::query();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        // Header row titles
        return [
            'ID',             // Column A
            'Product Name',   // Column B
            'Stock',          // Column C
            'Unit Price',     // Column D
            'Total Value',    // Column E
        ];
    }

    /**
     * @param mixed $product
     * @return array
     */
    public function map($product): array
    {
        // Data for each row
        return [
            $product->productID,
            $product->productName,
            $product->stockQuantity,
            $product->price, // Keep as raw number for formatting
            $product->stockQuantity * $product->price, // Keep as raw number
        ];
    }

    /**
     * Define specific column widths.
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 10, // ID column width
            'B' => 45, // Product Name column width
            'C' => 10, // Stock column width
            'D' => 15, // Unit Price column width
            'E' => 15, // Total Value column width
        ];
    }

    /**
     * Define number formatting for columns.
     * @return array
     */
    public function columnFormats(): array
    {
        // Define custom format string for Philippine Peso (₱)
        $pesoFormat = '"₱"#,##0.00'; // Places ₱ symbol, uses comma separator, 2 decimal places

        return [
            'C' => NumberFormat::FORMAT_NUMBER, // Format Stock as plain number (or '#,##0' for thousands separator)
            'D' => $pesoFormat,                 // Format Unit Price using Peso format
            'E' => $pesoFormat,                 // Format Total Value using Peso format
        ];
    }

    /**
     * Apply styles to the worksheet.
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        // Make the entire first row (headers) bold.
        $headerRow = 5;
        $sheet->getStyle($headerRow)->getFont()->setBold(true);

        // Align currency/number columns to the right, starting from the header row
        $sheet->getStyle('C' . $headerRow . ':E' . $sheet->getHighestRow())
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    }

    /**
     * Register events to modify the sheet after data is written.
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // ---- Add Summary Rows at the Top ----
                $sheet->insertNewRowBefore(1, 4); // Insert 4 new rows for Title, Date Range, Sales Data

                // Merge cells for title
                $sheet->mergeCells('A1:E1');
                $sheet->setCellValue('A1', 'Inventory and Sales Summary');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Add Date Range Display
                $sheet->mergeCells('A2:E2'); // Merge cells for date range display
                $sheet->setCellValue('A2', "Date Range: " . $this->dateRangeDisplay);
                $sheet->getStyle('A2')->getFont()->setBold(true);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);


                // Add Sales Data (starting on row 3 now)
                $sheet->setCellValue('A3', "Orders in Range:");
                $sheet->setCellValue('B3', $this->salesOrdersCount); // Use stored property
                $sheet->getStyle('A3')->getFont()->setBold(true);
                // Apply number format if needed (e.g., thousands separator)
                $sheet->getStyle('B3')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);

                $sheet->setCellValue('A4', "Sales Total in Range:");
                $sheet->setCellValue('B4', $this->salesTotalValue); // Use stored property
                $sheet->getStyle('A4')->getFont()->setBold(true);
                // Apply Peso formatting to the sales total cell
                $sheet->getStyle('B4')->getNumberFormat()->setFormatCode('"₱"#,##0.00');

                // Note: The original product headers (ID, Product Name...) now start on row 5
                // The styles() and columnFormats() methods were adjusted for this.

                // Optional: Add Inventory Summary data here too if needed
                // You would need to pass $totalValue etc. via the constructor as well
                // $sheet->setCellValue('D3', "Total Inventory Value:");
                // $sheet->setCellValue('E3', $this->totalValue); // Example if passed
                // $sheet->getStyle('E3')->getNumberFormat()->setFormatCode('"₱"#,##0.00');
            },
        ];
    }
}
