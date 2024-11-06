<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NaturalPersonResource\Pages;
use App\Models\NaturalPerson;
use App\Services\CartorialValidationService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
            Tabs::make('Cadastro')
                ->tabs([
                    // Tab Dados Pessoais
                    self::getDadosPessoaisTab(),

                    // Tab Endereços
                    self::getEnderecosTab(),

                    // Tab Contatos
                    self::getContatosTab(),
                ])
                ->columnSpanFull(),
        ]);
    }

    private static function getDadosPessoaisTab(): Tabs\Tab
    {
        return Tabs\Tab::make('Dados Pessoais')
            ->schema([
                TextInput::make('nome')
                    ->label('Nome Completo')
                    ->required()
                    ->maxLength(255),

                TextInput::make('nome_social')
                    ->label('Nome Social')
                    ->maxLength(255),

                TextInput::make('cpf')
                    ->label('CPF')
                    ->required()
                    ->mask('999.999.999-99')
                    ->unique(ignoreRecord: true)
                    ->dehydrateStateUsing(fn (?string $state): ?string =>
                        $state ? preg_replace('/[^0-9]/', '', $state) : null)
                    ->rules(function (CartorialValidationService $validator) {
                        return [
                            'required',
                            function($attribute, $value, $fail) use ($validator) {
                                if (!$validator->validateCpf($value)) {
                                    $fail('O CPF informado não é válido.');
                                }
                            }
                        ];
                    }),

                TextInput::make('rg')
                    ->label('RG')
                    ->required()
                    ->maxLength(20),

                TextInput::make('orgao_expedidor')
                    ->label('Órgão Expedidor')
                    ->required()
                    ->maxLength(10),

                Select::make('uf_expedidor')
                    ->label('UF Expedidor')
                    ->required()
                    ->options(self::getEstadosBrasileiros()),

                DatePicker::make('data_emissao_rg')
                    ->label('Data de Emissão RG')
                    ->required()
                    ->maxDate(now()),

                DatePicker::make('data_nascimento')
                    ->label('Data de Nascimento')
                    ->required()
                    ->maxDate(now())
                    ->minDate(now()->subYears(150)),

                TextInput::make('naturalidade')
                    ->label('Naturalidade')
                    ->required()
                    ->maxLength(255),

                TextInput::make('nacionalidade')
                    ->label('Nacionalidade')
                    ->default('Brasileira')
                    ->required()
                    ->maxLength(255),

                Select::make('estado_civil')
                    ->label('Estado Civil')
                    ->required()
                    ->options([
                        'solteiro' => 'Solteiro(a)',
                        'casado' => 'Casado(a)',
                        'divorciado' => 'Divorciado(a)',
                        'viuvo' => 'Viúvo(a)',
                        'uniao_estavel' => 'União Estável'
                    ]),

                TextInput::make('conjuge')
                    ->label('Nome do Cônjuge')
                    ->maxLength(255)
                    ->hidden(fn (Get $get): bool =>
                        !in_array($get('estado_civil'), ['casado', 'uniao_estavel'])),

                TextInput::make('mae')
                    ->label('Nome da Mãe')
                    ->required()
                    ->maxLength(255),

                TextInput::make('pai')
                    ->label('Nome do Pai')
                    ->maxLength(255),

                TextInput::make('profissao')
                    ->label('Profissão')
                    ->required()
                    ->maxLength(255),
            ])
            ->columns(2);
    }

    private static function getEnderecosTab(): Tabs\Tab
    {
        return Tabs\Tab::make('Endereços')
            ->schema([
                Repeater::make('addresses')
                    ->relationship()
                    ->schema([
                        Select::make('tipo')
                            ->label('Tipo de Endereço')
                            ->required()
                            ->options([
                                'residencial' => 'Residencial',
                                'comercial' => 'Comercial',
                                'correspondencia' => 'Correspondência'
                            ]),

                        TextInput::make('cep')
                            ->label('CEP')
                            ->required()
                            ->mask('99999-999')
                            ->live()
                            ->dehydrateStateUsing(fn (?string $state): ?string =>
                                $state ? preg_replace('/[^0-9]/', '', $state) : null)
                            ->afterStateUpdated(function (CartorialValidationService $validator, ?string $state, Set $set) {
                                if (!$state || strlen(preg_replace('/[^0-9]/', '', $state)) !== 8) {
                                    return;
                                }

                                try {
                                    $endereco = $validator->fetchAddressByCep($state);

                                    $set('logradouro', $endereco['logradouro'] ?? '');
                                    $set('bairro', $endereco['bairro'] ?? '');
                                    $set('cidade', $endereco['cidade'] ?? '');
                                    $set('uf', $endereco['uf'] ?? '');

                                    if (!empty($endereco['complemento'])) {
                                        $set('complemento', $endereco['complemento']);
                                    }

                                    Notification::make()
                                        ->title('Endereço encontrado')
                                        ->success()
                                        ->send();
                                } catch (\Exception $e) {
                                    Notification::make()
                                        ->title('Erro ao buscar CEP')
                                        ->body($e->getMessage())
                                        ->danger()
                                        ->send();
                                }
                            }),

                        TextInput::make('logradouro')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('numero')
                            ->required()
                            ->maxLength(20),

                        TextInput::make('complemento')
                            ->maxLength(255),

                        TextInput::make('bairro')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('cidade')
                            ->required()
                            ->maxLength(255),

                        Select::make('uf')
                            ->required()
                            ->options(self::getEstadosBrasileiros()),
                    ])
                    ->columns(2),
            ]);
    }

    private static function getContatosTab(): Tabs\Tab
    {
        return Tabs\Tab::make('Contatos')
            ->schema([
                Repeater::make('contacts')
                    ->relationship()
                    ->schema([
                        TextInput::make('telefone')
                            ->label('Telefone Fixo')
                            ->tel()
                            ->mask('(99) 9999-9999'),

                        TextInput::make('celular')
                            ->label('Celular')
                            ->tel()
                            ->mask('(99) 99999-9999'),

                        TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->maxLength(255),

                        TextInput::make('contato_alternativo')
                            ->label('Contato Alternativo')
                            ->maxLength(255),
                    ])
                    ->columns(2),
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
                    ->formatStateUsing(fn (string $state): string =>
                        substr($state, 0, 3) . '.' .
                        substr($state, 3, 3) . '.' .
                        substr($state, 6, 3) . '-' .
                        substr($state, 9, 2))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('rg')
                    ->label('RG')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Cadastrado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNaturalPeople::route('/'),
            'create' => Pages\CreateNaturalPerson::route('/create'),
            'edit' => Pages\EditNaturalPerson::route('/{record}/edit'),
            'view' => Pages\ViewNaturalPerson::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    private static function getEstadosBrasileiros(): array
    {
        return [
            'AC' => 'Acre',
            'AL' => 'Alagoas',
            'AP' => 'Amapá',
            'AM' => 'Amazonas',
            'BA' => 'Bahia',
            'CE' => 'Ceará',
            'DF' => 'Distrito Federal',
            'ES' => 'Espírito Santo',
            'GO' => 'Goiás',
            'MA' => 'Maranhão',
            'MT' => 'Mato Grosso',
            'MS' => 'Mato Grosso do Sul',
            'MG' => 'Minas Gerais',
            'PA' => 'Pará',
            'PB' => 'Paraíba',
            'PR' => 'Paraná',
            'PE' => 'Pernambuco',
            'PI' => 'Piauí',
            'RJ' => 'Rio de Janeiro',
            'RN' => 'Rio Grande do Norte',
            'RS' => 'Rio Grande do Sul',
            'RO' => 'Rondônia',
            'RR' => 'Roraima',
            'SC' => 'Santa Catarina',
            'SP' => 'São Paulo',
            'SE' => 'Sergipe',
            'TO' => 'Tocantins',
        ];
    }
}
