<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NaturalPersonResource\Pages;
use App\Models\NaturalPerson;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NaturalPersonResource extends Resource
{
    protected static ?string $model = NaturalPerson::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $modelLabel = 'Pessoa Física';
    protected static ?string $pluralModelLabel = 'Pessoas Físicas';
    protected static ?string $navigationGroup = 'Cadastros';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Cadastro')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('Dados Pessoais')
                        ->schema([
                            Forms\Components\TextInput::make('nome')
                                ->label('Nome Completo')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('nome_social')
                                ->label('Nome Social')
                                ->maxLength(255),
                            Forms\Components\TextInput::make('cpf')
                                ->label('CPF')
                                ->required()
                                ->mask('999.999.999-99')
                                ->maxLength(14)
                                ->unique(ignoreRecord: true),
                            Forms\Components\Grid::make(3)->schema([
                                Forms\Components\TextInput::make('rg')
                                    ->label('RG')
                                    ->required()
                                    ->maxLength(20),
                                Forms\Components\TextInput::make('orgao_expedidor')
                                    ->label('Órgão Expedidor')
                                    ->required()
                                    ->maxLength(10),
                                Forms\Components\Select::make('uf_expedidor')
                                    ->label('UF Expedidor')
                                    ->required()
                                    ->options([
                                        'AC' => 'Acre', 'AL' => 'Alagoas', 'AP' => 'Amapá',
                                        'AM' => 'Amazonas', 'BA' => 'Bahia', 'CE' => 'Ceará',
                                        'DF' => 'Distrito Federal', 'ES' => 'Espírito Santo',
                                        'GO' => 'Goiás', 'MA' => 'Maranhão', 'MT' => 'Mato Grosso',
                                        'MS' => 'Mato Grosso do Sul', 'MG' => 'Minas Gerais',
                                        'PA' => 'Pará', 'PB' => 'Paraíba', 'PR' => 'Paraná',
                                        'PE' => 'Pernambuco', 'PI' => 'Piauí', 'RJ' => 'Rio de Janeiro',
                                        'RN' => 'Rio Grande do Norte', 'RS' => 'Rio Grande do Sul',
                                        'RO' => 'Rondônia', 'RR' => 'Roraima', 'SC' => 'Santa Catarina',
                                        'SP' => 'São Paulo', 'SE' => 'Sergipe', 'TO' => 'Tocantins'
                                    ])
                            ]),
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\DatePicker::make('data_emissao_rg')
                                    ->label('Data de Emissão RG')
                                    ->required()
                                    ->maxDate(now()),
                                Forms\Components\DatePicker::make('data_nascimento')
                                    ->label('Data de Nascimento')
                                    ->required()
                                    ->maxDate(now()),
                            ]),
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\TextInput::make('naturalidade')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('nacionalidade')
                                    ->required()
                                    ->default('Brasileira')
                                    ->maxLength(255),
                            ]),
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\Select::make('estado_civil')
                                    ->label('Estado Civil')
                                    ->required()
                                    ->options([
                                        'solteiro' => 'Solteiro(a)',
                                        'casado' => 'Casado(a)',
                                        'divorciado' => 'Divorciado(a)',
                                        'viuvo' => 'Viúvo(a)',
                                        'uniao_estavel' => 'União Estável'
                                    ]),
                                Forms\Components\TextInput::make('conjuge')
                                    ->label('Cônjuge')
                                    ->visible(fn (Forms\Get $get): bool =>
                                        in_array($get('estado_civil'), ['casado', 'uniao_estavel']))
                                    ->maxLength(255),
                            ]),
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\TextInput::make('mae')
                                    ->label('Nome da Mãe')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('pai')
                                    ->label('Nome do Pai')
                                    ->maxLength(255),
                            ]),
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\TextInput::make('profissao')
                                    ->label('Profissão')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('renda_mensal')
                                    ->label('Renda Mensal')
                                    ->numeric()
                                    ->prefix('R$')
                                    ->maxValue(999999999.99),
                            ]),
                        ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Endereços')
                        ->schema([
                            Forms\Components\Repeater::make('addresses')
                                ->relationship()
                                ->schema([
                                    Forms\Components\Select::make('tipo')
                                        ->label('Tipo de Endereço')
                                        ->required()
                                        ->options([
                                            'residencial' => 'Residencial',
                                            'comercial' => 'Comercial',
                                            'correspondencia' => 'Correspondência'
                                        ]),
                                    Forms\Components\TextInput::make('cep')
                                        ->label('CEP')
                                        ->required()
                                        ->mask('99999-999'),
                                    Forms\Components\TextInput::make('logradouro')
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\Grid::make(3)->schema([
                                        Forms\Components\TextInput::make('numero')
                                            ->required()
                                            ->maxLength(20),
                                        Forms\Components\TextInput::make('complemento')
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('bairro')
                                            ->required()
                                            ->maxLength(255),
                                    ]),
                                    Forms\Components\Grid::make(2)->schema([
                                        Forms\Components\TextInput::make('cidade')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\Select::make('uf')
                                            ->required()
                                            ->options([
                                                'AC' => 'Acre', 'AL' => 'Alagoas', 'AP' => 'Amapá',
                                                'AM' => 'Amazonas', 'BA' => 'Bahia', 'CE' => 'Ceará',
                                                'DF' => 'Distrito Federal', 'ES' => 'Espírito Santo',
                                                'GO' => 'Goiás', 'MA' => 'Maranhão', 'MT' => 'Mato Grosso',
                                                'MS' => 'Mato Grosso do Sul', 'MG' => 'Minas Gerais',
                                                'PA' => 'Pará', 'PB' => 'Paraíba', 'PR' => 'Paraná',
                                                'PE' => 'Pernambuco', 'PI' => 'Piauí', 'RJ' => 'Rio de Janeiro',
                                                'RN' => 'Rio Grande do Norte', 'RS' => 'Rio Grande do Sul',
                                                'RO' => 'Rondônia', 'RR' => 'Roraima', 'SC' => 'Santa Catarina',
                                                'SP' => 'São Paulo', 'SE' => 'Sergipe', 'TO' => 'Tocantins'
                                            ]),
                                    ]),
                                ])
                                ->columns(2),
                        ]),

                    Forms\Components\Tabs\Tab::make('Contatos')
                        ->schema([
                            Forms\Components\Repeater::make('contacts')
                                ->relationship()
                                ->schema([
                                    Forms\Components\TextInput::make('telefone')
                                        ->tel()
                                        ->mask('(99) 9999-9999'),
                                    Forms\Components\TextInput::make('celular')
                                        ->tel()
                                        ->mask('(99) 99999-9999'),
                                    Forms\Components\TextInput::make('email')
                                        ->email()
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('contato_alternativo')
                                        ->maxLength(255),
                                ])
                                ->columns(2),
                        ]),
                ])->columnSpanFull()
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cpf')
                    ->label('CPF')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rg')
                    ->label('RG')
                    ->searchable(),
                Tables\Columns\TextColumn::make('data_nascimento')
                    ->label('Data de Nascimento')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Cadastrado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNaturalPeople::route('/'),
            'create' => Pages\CreateNaturalPerson::route('/create'),
            'view' => Pages\ViewNaturalPerson::route('/{record}'),
            'edit' => Pages\EditNaturalPerson::route('/{record}/edit'),
        ];
    }
}
