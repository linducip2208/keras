<?php

namespace App\Filament\Resources\Employees\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Employee')
                ->columnSpanFull()
                ->tabs([
                    Tab::make('Profil')
                        ->icon('heroicon-o-user')
                        ->schema([
                            Section::make('Identitas Karyawan')
                                ->columns(2)
                                ->schema([
                                    TextInput::make('employee_no')->label('Nomor Karyawan')->required()->placeholder('EMP-2026-0001'),
                                    TextInput::make('full_name')->label('Nama Lengkap')->required(),
                                    TextInput::make('nickname')->label('Panggilan'),
                                    Select::make('gender')->label('Jenis Kelamin')
                                        ->options(['M' => 'Laki-laki', 'F' => 'Perempuan'])->required(),
                                    TextInput::make('place_of_birth')->label('Tempat Lahir'),
                                    DatePicker::make('date_of_birth')->label('Tanggal Lahir')->native(false),
                                    Select::make('religion')->label('Agama')
                                        ->options(['islam'=>'Islam','christian'=>'Kristen','catholic'=>'Katolik','hindu'=>'Hindu','buddha'=>'Buddha','confucian'=>'Konghucu','other'=>'Lainnya']),
                                    Select::make('marital_status')->label('Status Pernikahan')
                                        ->options(['single'=>'Belum Menikah','married'=>'Menikah','divorced'=>'Cerai','widowed'=>'Janda/Duda'])->default('single')->required(),
                                    Select::make('blood_type')->label('Golongan Darah')
                                        ->options(['A'=>'A','B'=>'B','AB'=>'AB','O'=>'O']),
                                    TextInput::make('nationality')->label('Kewarganegaraan')->default('IDN')->required(),
                                ]),

                            Section::make('Identitas Pajak (PII Encrypted)')
                                ->columns(2)
                                ->collapsible()
                                ->schema([
                                    TextInput::make('nik')->label('NIK (16 digit)')->maxLength(16)->password()->revealable(),
                                    TextInput::make('npwp')->label('NPWP')->password()->revealable(),
                                    Select::make('ptkp_status')->label('Status PTKP')
                                        ->options([
                                            'TK_0'=>'TK/0','TK_1'=>'TK/1','TK_2'=>'TK/2','TK_3'=>'TK/3',
                                            'K_0'=>'K/0','K_1'=>'K/1','K_2'=>'K/2','K_3'=>'K/3',
                                        ])->default('TK_0')->required()->columnSpanFull(),
                                ]),

                            Section::make('Alamat & Kontak')
                                ->columns(2)
                                ->schema([
                                    Textarea::make('ktp_address')->label('Alamat KTP')->rows(2)->columnSpanFull(),
                                    Textarea::make('current_address')->label('Alamat Domisili')->rows(2)->columnSpanFull(),
                                    TextInput::make('postal_code')->label('Kode Pos'),
                                    TextInput::make('phone')->label('Nomor HP Utama')->tel(),
                                    TextInput::make('phone_alt')->label('Nomor HP Cadangan')->tel(),
                                    TextInput::make('personal_email')->label('Email Pribadi')->email(),
                                    TextInput::make('employee_email')->label('Email Kantor')->email(),
                                ]),

                            Section::make('Foto & Dokumen')
                                ->columns(2)
                                ->schema([
                                    FileUpload::make('photo_path')->label('Foto Profil')->image()->avatar()->directory('employees/photos'),
                                    FileUpload::make('signature_path')->label('Tanda Tangan')->image()->directory('employees/signatures'),
                                ]),
                        ]),

                    Tab::make('Organisasi')
                        ->icon('heroicon-o-building-office-2')
                        ->schema([
                            Section::make('Penempatan')
                                ->columns(2)
                                ->schema([
                                    Select::make('company_id')->label('Perusahaan')
                                        ->relationship('company', 'name')->required()->searchable()->preload(),
                                    Select::make('branch_id')->label('Cabang')
                                        ->relationship('branch', 'name')->searchable()->preload(),
                                    Select::make('department_id')->label('Departemen')
                                        ->relationship('department', 'name')->searchable()->preload(),
                                    Select::make('position_id')->label('Jabatan')
                                        ->relationship('position', 'name')->searchable()->preload(),
                                    Select::make('job_title_id')->label('Job Title')
                                        ->relationship('jobTitle', 'name')->searchable()->preload(),
                                    Select::make('job_level_id')->label('Level')
                                        ->relationship('jobLevel', 'name')->searchable()->preload(),
                                ]),

                            Section::make('Reporting Line')
                                ->columns(2)
                                ->schema([
                                    Select::make('manager_employee_id')->label('Manager (Direct)')
                                        ->relationship('manager', 'full_name')->searchable()->preload(),
                                    Select::make('dotted_line_manager_id')->label('Manager (Dotted-line)')
                                        ->relationship('manager', 'full_name', fn ($q) => $q->whereKeyNot(request()->route('record')))->searchable()->preload(),
                                ]),

                            Section::make('Status Kepegawaian')
                                ->columns(2)
                                ->schema([
                                    DatePicker::make('join_date')->label('Tanggal Bergabung')->native(false),
                                    DatePicker::make('probation_end_date')->label('Akhir Probation')->native(false),
                                    Select::make('employment_status')->label('Status')
                                        ->options([
                                            'active'=>'Aktif','probation'=>'Probation','on_leave'=>'Cuti Panjang',
                                            'suspended'=>'Suspended','resigned'=>'Resign','terminated'=>'Diberhentikan',
                                        ])->default('probation')->required(),
                                    Select::make('employment_type')->label('Jenis Karyawan')
                                        ->options([
                                            'permanent'=>'Permanent (PKWTT)','contract'=>'Kontrak (PKWT)','probation'=>'Probation',
                                            'intern'=>'Magang','outsource'=>'Outsource','freelance'=>'Freelance',
                                        ])->default('probation')->required(),
                                    DatePicker::make('last_working_date')->label('Last Working Date')->native(false),
                                    TextInput::make('qr_badge_code')->label('QR Badge ID'),
                                ]),
                        ]),

                    Tab::make('Akun Bank')
                        ->icon('heroicon-o-credit-card')
                        ->schema([
                            \Filament\Schemas\Components\Utilities\Get::make('id'),
                            Section::make('Rekening Bank (untuk transfer gaji)')
                                ->description('Karyawan bisa punya >1 rekening dengan split allocation. Tambah/edit di tab Akun Bank setelah simpan profil.')
                                ->schema([]),
                        ]),

                    Tab::make('Keluarga')
                        ->icon('heroicon-o-user-group')
                        ->schema([
                            Section::make('Data Keluarga (BPJS dependents & PTKP)')
                                ->description('Tambah anggota keluarga setelah simpan profil untuk dependent BPJS Kesehatan dan PTKP.')
                                ->schema([]),
                        ]),

                    Tab::make('Tenant')
                        ->visible(fn () => auth()->user()?->is_super_admin)
                        ->schema([
                            Select::make('tenant_id')->relationship('tenant', 'name')->required(),
                            Select::make('user_id')->label('Linked User')->relationship('user', 'email')->searchable(),
                        ]),
                ]),
        ]);
    }
}
