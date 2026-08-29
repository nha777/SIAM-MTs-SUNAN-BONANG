import { useState } from 'react';
import { motion } from 'motion/react';
import { 
  FolderTree, 
  Settings, 
  ShieldCheck, 
  FileJson, 
  Terminal, 
  Database, 
  CheckCircle, 
  Play, 
  ExternalLink,
  Code2,
  GitBranch,
  Search,
  BookOpen,
  Eye,
  Lock,
  Boxes,
  Activity,
  UserCheck
} from 'lucide-react';

// Structuring file content descriptions for the codebase viewer
interface FileItem {
  name: string;
  type: 'file' | 'folder';
  path: string;
  description: string;
  badge?: string;
  children?: FileItem[];
  codeSnippet?: string;
}

export default function App() {
  const [activeTab, setActiveTab] = useState<'architecture' | 'database' | 'ci-cd' | 'rbac'>('architecture');
  const [selectedFile, setSelectedFile] = useState<FileItem | null>(null);
  const [searchQuery, setSearchQuery] = useState('');
  const [copiedCode, setCopiedCode] = useState(false);

  const bootstrapStatus = {
    structure: true,
    composer: true,
    linting: true,
    actions: true,
    auditLogs: true,
    authSkeleton: true
  };

  const filesStructure: FileItem[] = [
    {
      name: 'app',
      type: 'folder',
      path: '/app',
      description: 'Directory inti logika backend Laravel.',
      children: [
        {
          name: 'Providers',
          type: 'folder',
          path: '/app/Providers',
          description: 'Penyedia layanan sistem.',
          children: [
            {
              name: 'ModuleServiceProvider.php',
              type: 'file',
              path: '/app/Providers/ModuleServiceProvider.php',
              description: 'Penyedia modul dinamis untuk mendeteksi sub-direktori app/Modules secara otomatis.',
              badge: 'Dynamic Autoload',
              codeSnippet: `<?php\n\nnamespace App\\Providers;\n\nuse Illuminate\\Support\\ServiceProvider;\nuse Illuminate\\Support\\Facades\\File;\n\nclass ModuleServiceProvider extends ServiceProvider\n{\n    public function register(): void\n    {\n        $modulesPath = app_path('Modules');\n        // Membaca semua subdirektori modul...\n    }\n}`
            }
          ]
        },
        {
          name: 'Modules',
          type: 'folder',
          path: '/app/Modules',
          description: 'Domain modular monolith tempat pengisolasian fitur bisnis.',
          children: [
            {
              name: 'Base',
              type: 'folder',
              path: '/app/Modules/Base',
              description: 'Modul dasar penyedia kontrak, base repository, dan base service.',
              children: [
                {
                  name: 'Contracts',
                  type: 'folder',
                  path: '/app/Modules/Base/Contracts',
                  description: 'Kumpulan antarmuka (interfaces) penjamin SOLID.',
                  children: [
                    {
                      name: 'BaseRepositoryInterface.php',
                      type: 'file',
                      path: '/app/Modules/Base/Contracts/BaseRepositoryInterface.php',
                      description: 'Kontrak wajib untuk semua operasi manipulasi basis data dasar.',
                      codeSnippet: `<?php\n\nnamespace App\\Modules\\Base\\Contracts;\n\ninterface BaseRepositoryInterface\n{\n    public function all(array $columns = ['*'], array $relations = []): Collection;\n    public function create(array $details): Model;\n}`
                    },
                    {
                      name: 'BaseServiceInterface.php',
                      type: 'file',
                      path: '/app/Modules/Base/Contracts/BaseServiceInterface.php',
                      description: 'Kontrak wajib untuk pembungkusan logika bisnis transaksional.',
                      codeSnippet: `<?php\n\nnamespace App\\Modules\\Base\\Contracts;\n\ninterface BaseServiceInterface\n{\n    public function store(array $data): Model;\n    public function update(int|string $id, array $data): bool;\n}`
                    }
                  ]
                },
                {
                  name: 'Repositories',
                  type: 'folder',
                  path: '/app/Modules/Base/Repositories',
                  description: 'Implementasi abstrak dari Repository Pattern.',
                  children: [
                    {
                      name: 'BaseRepository.php',
                      type: 'file',
                      path: '/app/Modules/Base/Repositories/BaseRepository.php',
                      description: 'Menjembatani akses data tanpa mengizinkan kueri Eloquent langsung di Service.',
                      codeSnippet: `<?php\n\nabstract class BaseRepository implements BaseRepositoryInterface\n{\n    protected Model $model;\n    public function __construct(Model $model) { $this->model = $model; }\n}`
                    }
                  ]
                },
                {
                  name: 'Services',
                  type: 'folder',
                  path: '/app/Modules/Base/Services',
                  description: 'Implementasi abstrak Service Layer.',
                  children: [
                    {
                      name: 'BaseService.php',
                      type: 'file',
                      path: '/app/Modules/Base/Services/BaseService.php',
                      description: 'Membungkus manipulasi ganda di dalam transaksi basis data otomatis.',
                      codeSnippet: `<?php\n\nabstract class BaseService implements BaseServiceInterface\n{\n    public function store(array $data): Model {\n        DB::beginTransaction();\n        try { ... DB::commit(); } catch(Exception $e) { DB::rollBack(); }\n    }\n}`
                    }
                  ]
                },
                {
                  name: 'Traits',
                  type: 'folder',
                  path: '/app/Modules/Base/Traits',
                  description: 'Pustaka modular fungsionalitas asinkronus.',
                  children: [
                    {
                      name: 'HasAuditLogs.php',
                      type: 'file',
                      path: '/app/Modules/Base/Traits/HasAuditLogs.php',
                      description: 'Trait otomatis pencatat jejak audit immutable ke basis data.',
                      badge: 'Security',
                      codeSnippet: `<?php\n\ntrait HasAuditLogs\n{\n    public static function bootHasAuditLogs(): void {\n        static::updated(function ($model) { ... });\n    }\n}`
                    }
                  ]
                }
              ]
            },
            {
              name: 'Auth',
              type: 'folder',
              path: '/app/Modules/Auth',
              description: 'Modul otorisasi & manajemen pengguna.',
              children: [
                {
                  name: 'Models',
                  type: 'folder',
                  path: '/app/Modules/Auth/Models',
                  description: 'Representasi model database User terisolasi.',
                  children: [
                    {
                      name: 'User.php',
                      type: 'file',
                      path: '/app/Modules/Auth/Models/User.php',
                      description: 'Model otentikasi dengan fitur audit log dan hak akses Spatie.',
                      codeSnippet: `<?php\n\nclass User extends Authenticatable\n{\n    use HasRoles, HasAuditLogs, SoftDeletes;\n}`
                    }
                  ]
                },
                {
                  name: 'Services',
                  type: 'folder',
                  path: '/app/Modules/Auth/Services',
                  description: 'Logika masuk & keluar aplikasi murni.',
                  children: [
                    {
                      name: 'AuthService.php',
                      type: 'file',
                      path: '/app/Modules/Auth/Services/AuthService.php',
                      description: 'Memproses login dan memicu pencatatan audit log masuk.',
                      codeSnippet: `<?php\n\nclass AuthService extends BaseService\n{\n    public function login($email, $password) { ... }\n}`
                    }
                  ]
                }
              ]
            }
          ]
        }
      ]
    },
    {
      name: 'database',
      type: 'folder',
      path: '/database',
      description: 'Migrasi & seeding basis data.',
      children: [
        {
          name: 'migrations',
          type: 'folder',
          path: '/database/migrations',
          description: 'Arsitektur database aman terenkripsi.',
          children: [
            {
              name: '2026_07_16_000000_create_users_table.php',
              type: 'file',
              path: '/database/migrations/2026_07_16_000000_create_users_table.php',
              description: 'Tabel pengguna dengan integrasi is_active dan softDeletes.',
              codeSnippet: `$table->id();\n$table->string('name');\n$table->string('email')->unique();\n$table->boolean('is_active')->default(true);\n$table->softDeletes();`
            },
            {
              name: '2026_07_16_000001_create_audit_logs_table.php',
              type: 'file',
              path: '/database/migrations/2026_07_16_000001_create_audit_logs_table.php',
              description: 'Arsitektur audit log immutable dengan pengenal event_id, request_id, dan severity.',
              badge: 'Immutable Schema',
              codeSnippet: `$table->id();\n$table->uuid('event_id')->unique();\n$table->uuid('request_id')->index();\n$table->enum('severity', ['info', 'warning', 'critical']);\n$table->json('old_values')->nullable();\n$table->json('new_values')->nullable();`
            }
          ]
        }
      ]
    },
    {
      name: '.github',
      type: 'folder',
      path: '/.github',
      description: 'Otomatisasi pengujian dan integrasi berkelanjutan.',
      children: [
        {
          name: 'workflows',
          type: 'folder',
          path: '/.github/workflows',
          description: 'Alur kerja GitHub Actions.',
          children: [
            {
              name: 'ci.yml',
              type: 'file',
              path: '/.github/workflows/ci.yml',
              description: 'Alur CI otomatis untuk mengecek Laravel Pint, PHPStan static analysis, dan Pest testing.',
              badge: 'DevOps',
              codeSnippet: `name: SIAM CI\non: [push, pull_request]\njobs:\n  laravel-pint:\n  phpstan-analysis:\n  pest-tests:`
            }
          ]
        }
      ]
    },
    {
      name: 'composer.json',
      type: 'file',
      path: '/composer.json',
      description: 'Konfigurasi dependensi backend, Larastan, Pest, dan Pint formatter.',
      badge: 'Package Config',
      codeSnippet: `{\n  "require": {\n    "php": "^8.3",\n    "laravel/framework": "^12.0",\n    "spatie/laravel-permission": "^6.4"\n  }\n}`
    },
    {
      name: 'phpstan.neon',
      type: 'file',
      path: '/phpstan.neon',
      description: 'Konfigurasi static analysis Larastan level 5.',
      codeSnippet: `includes:\n    - vendor/larastan/larastan/extension.neon\nparameters:\n    paths:\n        - app/\n    level: 5`
    },
    {
      name: 'pint.json',
      type: 'file',
      path: '/pint.json',
      description: 'Penyamaan standar format kode Laravel Pint.',
      codeSnippet: `{\n  "preset": "laravel",\n  "rules": {\n    "align_multiline_comment": true\n  }\n}`
    }
  ];

  const handleCopy = (text: string) => {
    navigator.clipboard.writeText(text);
    setCopiedCode(true);
    setTimeout(() => setCopiedCode(false), 2000);
  };

  const renderFileTree = (items: FileItem[], depth = 0) => {
    return items.map((item) => {
      const isFolder = item.type === 'folder';
      const hasSearchMatch = item.name.toLowerCase().includes(searchQuery.toLowerCase()) || 
                             item.description.toLowerCase().includes(searchQuery.toLowerCase());

      if (searchQuery && !hasSearchMatch && !isFolder) {
        return null;
      }

      return (
        <div key={item.path} style={{ paddingLeft: `${depth * 14}px` }} className="my-1">
          <div 
            onClick={() => !isFolder && setSelectedFile(item)}
            className={`flex items-center justify-between p-1.5 rounded-lg transition-all cursor-pointer ${
              selectedFile?.path === item.path 
                ? 'bg-amber-500/10 border border-amber-500/20 text-amber-200' 
                : 'hover:bg-slate-800/50 text-slate-300'
            }`}
          >
            <div className="flex items-center gap-2">
              {isFolder ? (
                <FolderTree className="w-4 h-4 text-amber-500/80" />
              ) : (
                <FileJson className="w-4 h-4 text-cyan-400" />
              )}
              <span className={`text-xs ${isFolder ? 'font-medium text-slate-200' : 'font-mono'}`}>
                {item.name}
              </span>
            </div>
            {item.badge && (
              <span className="text-[10px] bg-slate-800 text-slate-400 px-1.5 py-0.5 rounded font-sans border border-slate-700/50">
                {item.badge}
              </span>
            )}
          </div>
          {isFolder && item.children && renderFileTree(item.children, depth + 1)}
        </div>
      );
    });
  };

  return (
    <div className="min-h-screen bg-slate-950 text-slate-100 flex flex-col font-sans selection:bg-amber-500/30 select-none">
      {/* Top Navigation / Status Header */}
      <header className="border-b border-slate-800 bg-slate-900/60 backdrop-blur-md px-6 py-4 flex items-center justify-between sticky top-0 z-50" id="header_section">
        <div className="flex items-center gap-3">
          <div className="bg-amber-500 text-slate-950 p-1.5 rounded-lg shadow-lg shadow-amber-500/10">
            <Boxes className="w-6 h-6" />
          </div>
          <div>
            <div className="flex items-center gap-2">
              <h1 className="text-lg font-bold tracking-tight text-white">SIAM</h1>
              <span className="text-[10px] bg-amber-500/10 text-amber-300 border border-amber-500/20 px-2 py-0.5 rounded-full font-semibold">
                Sprint 0: Bootstrap Complete
              </span>
            </div>
            <p className="text-xs text-slate-400">Sistem Informasi Administrasi Madrasah • Laravel 12 Monolith</p>
          </div>
        </div>

        <div className="flex items-center gap-4 text-xs">
          <div className="flex items-center gap-2 bg-slate-900 border border-slate-800 px-3 py-1.5 rounded-lg">
            <span className="relative flex h-2 w-2">
              <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
              <span className="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            <span className="text-slate-300 font-mono text-[11px]">PHP 8.3 | Larastan Lvl 5</span>
          </div>
        </div>
      </header>

      {/* Main Container */}
      <div className="flex-1 flex" id="main_layout">
        {/* Left Sidebar: Interactive File Tree */}
        <aside className="w-80 border-r border-slate-900 bg-slate-950 p-5 flex flex-col gap-4" id="sidebar_file_tree">
          <div>
            <div className="flex items-center justify-between mb-2">
              <h3 className="text-xs font-semibold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                <GitBranch className="w-3.5 h-3.5 text-amber-500" />
                Struktur Repositori
              </h3>
            </div>
            <div className="relative">
              <Search className="w-3.5 h-3.5 text-slate-500 absolute left-3 top-2.5" />
              <input 
                type="text" 
                placeholder="Cari file atau deskripsi..."
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                className="w-full bg-slate-900 border border-slate-800 text-xs rounded-lg pl-8 pr-3 py-2 text-slate-300 placeholder-slate-500 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/30 transition-all"
              />
            </div>
          </div>

          <div className="flex-1 overflow-y-auto max-h-[calc(100vh-210px)] pr-1 custom-scrollbar">
            {renderFileTree(filesStructure)}
          </div>

          {/* Quick Info Box */}
          <div className="bg-slate-900/50 border border-slate-800/60 rounded-xl p-3 text-xs">
            <p className="text-slate-400 leading-relaxed">
              💡 <span className="text-slate-300 font-medium">Tip:</span> Klik salah satu berkas di atas untuk meninjau deskripsi arsitektur dan potongan kode dasar.
            </p>
          </div>
        </aside>

        {/* Center / Right Content Tabs */}
        <main className="flex-1 bg-slate-900/10 p-6 flex flex-col gap-6 overflow-y-auto" id="main_content_area">
          {/* Sub Navigation Tabs */}
          <div className="flex items-center justify-between border-b border-slate-900 pb-3">
            <div className="flex gap-2">
              <button 
                onClick={() => setActiveTab('architecture')}
                className={`flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-medium transition-all ${
                  activeTab === 'architecture' 
                    ? 'bg-slate-900 text-white border border-slate-800' 
                    : 'text-slate-400 hover:text-slate-200'
                }`}
              >
                <Code2 className="w-3.5 h-3.5 text-amber-400" />
                Arsitektur Monolith
              </button>
              <button 
                onClick={() => setActiveTab('database')}
                className={`flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-medium transition-all ${
                  activeTab === 'database' 
                    ? 'bg-slate-900 text-white border border-slate-800' 
                    : 'text-slate-400 hover:text-slate-200'
                }`}
              >
                <Database className="w-3.5 h-3.5 text-cyan-400" />
                Skema Database & Audit Log
              </button>
              <button 
                onClick={() => setActiveTab('ci-cd')}
                className={`flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-medium transition-all ${
                  activeTab === 'ci-cd' 
                    ? 'bg-slate-900 text-white border border-slate-800' 
                    : 'text-slate-400 hover:text-slate-200'
                }`}
              >
                <Terminal className="w-3.5 h-3.5 text-emerald-400" />
                Alur Kerja CI-CD
              </button>
              <button 
                onClick={() => setActiveTab('rbac')}
                className={`flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-medium transition-all ${
                  activeTab === 'rbac' 
                    ? 'bg-slate-900 text-white border border-slate-800' 
                    : 'text-slate-400 hover:text-slate-200'
                }`}
              >
                <ShieldCheck className="w-3.5 h-3.5 text-indigo-400" />
                Matriks Otorisasi RBAC
              </button>
            </div>

            <div className="text-[11px] text-slate-400 font-mono">
              Fase: <span className="text-amber-400">Core Fondasi</span>
            </div>
          </div>

          {/* Conditional Views */}
          {activeTab === 'architecture' && (
            <motion.div 
              initial={{ opacity: 0, y: 10 }}
              animate={{ opacity: 1, y: 0 }}
              className="grid grid-cols-1 lg:grid-cols-2 gap-6"
            >
              <div className="flex flex-col gap-6">
                <div className="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-5 shadow-sm">
                  <h3 className="text-sm font-bold text-white mb-4 flex items-center gap-2">
                    <Boxes className="w-4 h-4 text-amber-500" />
                    Strategi Modularisasi Terpilih
                  </h3>
                  <div className="flex flex-col gap-4 text-xs text-slate-300 leading-relaxed">
                    <p>
                      Sistem SIAM menggunakan **Modular Monolith** sesuai dengan <strong>ADR-001</strong>. Logika dan entitas modul disimpan dalam namespace masing-masing di bawah direktori <code>app/Modules/</code>.
                    </p>
                    <div className="border-l-2 border-amber-500/40 pl-3 py-1 bg-slate-950/40 rounded-r-lg my-1">
                      <span className="font-semibold text-slate-200">Prinsip Ketat:</span> Modul satu sama lain tidak boleh bersentuhan langsung di level kueri basis data. Modul Auth tidak mengekspos model data murninya ke modul keuangan kelak.
                    </div>
                    <p>
                      Setiap komunikasi lintas-modul menggunakan antarmuka kontrak (Interface) formal dan asinkron melalui Event Bus / Domain Events (<strong>ADR-003</strong>).
                    </p>
                  </div>
                </div>

                <div className="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-5">
                  <h3 className="text-sm font-bold text-white mb-3 flex items-center gap-2">
                    <Settings className="w-4 h-4 text-cyan-400" />
                    Checklist Fondasi Proyek (Sprint 0)
                  </h3>
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div className="flex items-start gap-2.5 bg-slate-950/35 p-3 rounded-xl border border-slate-800/50">
                      <CheckCircle className="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
                      <div>
                        <h4 className="text-xs font-semibold text-slate-200">Base Repository</h4>
                        <p className="text-[10px] text-slate-400">Implementasi boilerplate kueri aman</p>
                      </div>
                    </div>
                    <div className="flex items-start gap-2.5 bg-slate-950/35 p-3 rounded-xl border border-slate-800/50">
                      <CheckCircle className="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
                      <div>
                        <h4 className="text-xs font-semibold text-slate-200">Base Service</h4>
                        <p className="text-[10px] text-slate-400">Transaksi database & error logger</p>
                      </div>
                    </div>
                    <div className="flex items-start gap-2.5 bg-slate-950/35 p-3 rounded-xl border border-slate-800/50">
                      <CheckCircle className="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
                      <div>
                        <h4 className="text-xs font-semibold text-slate-200">Autoload Provider</h4>
                        <p className="text-[10px] text-slate-400">Deteksi dinamis submodul otomatis</p>
                      </div>
                    </div>
                    <div className="flex items-start gap-2.5 bg-slate-950/35 p-3 rounded-xl border border-slate-800/50">
                      <CheckCircle className="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
                      <div>
                        <h4 className="text-xs font-semibold text-slate-200">Audit Log Trait</h4>
                        <p className="text-[10px] text-slate-400">Mata elang aktivitas mutable</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              {/* Interactive File Preview Pane */}
              <div className="bg-slate-900/40 border border-slate-800/80 rounded-2xl flex flex-col h-[400px]">
                <div className="px-4 py-3 border-b border-slate-800 flex items-center justify-between bg-slate-900/60 rounded-t-2xl">
                  <div className="flex items-center gap-2">
                    <Terminal className="w-4 h-4 text-amber-500" />
                    <span className="text-xs font-mono text-slate-300">
                      {selectedFile ? selectedFile.path : 'Pilih file di kiri untuk melihat isi'}
                    </span>
                  </div>
                  {selectedFile?.codeSnippet && (
                    <button 
                      onClick={() => handleCopy(selectedFile.codeSnippet || '')}
                      className="text-[10px] bg-slate-800 hover:bg-slate-700 text-slate-300 px-2 py-1 rounded border border-slate-700 transition-all"
                    >
                      {copiedCode ? 'Disalin!' : 'Salin Kode'}
                    </button>
                  )}
                </div>
                <div className="flex-1 p-4 overflow-auto bg-slate-950 font-mono text-[11px] leading-relaxed text-slate-300 rounded-b-2xl">
                  {selectedFile ? (
                    <div>
                      <p className="text-amber-400/90 mb-3 font-sans border-b border-slate-800/60 pb-2">
                        // {selectedFile.description}
                      </p>
                      <pre className="text-slate-200">
                        <code>{selectedFile.codeSnippet || '// Kode tidak dapat ditampilkan'}</code>
                      </pre>
                    </div>
                  ) : (
                    <div className="h-full flex flex-col items-center justify-center text-slate-500 gap-2 font-sans">
                      <BookOpen className="w-8 h-8 text-slate-700 animate-pulse" />
                      <p className="text-xs">Silakan pilih berkas dari menu pohon direktori sebelah kiri.</p>
                    </div>
                  )}
                </div>
              </div>
            </motion.div>
          )}

          {activeTab === 'database' && (
            <motion.div 
              initial={{ opacity: 0, y: 10 }}
              animate={{ opacity: 1, y: 0 }}
              className="flex flex-col gap-6"
            >
              <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {/* Users table */}
                <div className="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-5">
                  <div className="flex items-center justify-between mb-4">
                    <h3 className="text-sm font-bold text-white flex items-center gap-2">
                      <Database className="w-4 h-4 text-cyan-400" />
                      Struktur Tabel `users`
                    </h3>
                    <span className="text-[10px] bg-slate-800 text-slate-400 px-2 py-0.5 rounded font-mono">SoftDeletes</span>
                  </div>
                  <div className="overflow-x-auto">
                    <table className="w-full text-left text-xs text-slate-300">
                      <thead>
                        <tr className="border-b border-slate-800 text-slate-400 text-[10px] uppercase font-mono">
                          <th className="py-2">Kolom</th>
                          <th className="py-2">Tipe</th>
                          <th className="py-2">Atribut</th>
                        </tr>
                      </thead>
                      <tbody className="font-mono divide-y divide-slate-800/50">
                        <tr>
                          <td className="py-2 text-cyan-400 font-bold">id</td>
                          <td className="py-2 text-slate-400">BIGINT</td>
                          <td className="py-2 text-emerald-400">Primary Key, Auto Inc</td>
                        </tr>
                        <tr>
                          <td className="py-2 text-slate-200">name</td>
                          <td className="py-2 text-slate-400">VARCHAR(255)</td>
                          <td className="py-2 text-slate-500">-</td>
                        </tr>
                        <tr>
                          <td className="py-2 text-slate-200">email</td>
                          <td className="py-2 text-slate-400">VARCHAR(255)</td>
                          <td className="py-2 text-amber-400">Unique Index</td>
                        </tr>
                        <tr>
                          <td className="py-2 text-slate-200">password</td>
                          <td className="py-2 text-slate-400">VARCHAR(255)</td>
                          <td className="py-2 text-slate-500">Bcrypt Hash</td>
                        </tr>
                        <tr>
                          <td className="py-2 text-slate-200">is_active</td>
                          <td className="py-2 text-slate-400">TINYINT(1)</td>
                          <td className="py-2 text-slate-300">Default: 1 (True)</td>
                        </tr>
                        <tr>
                          <td className="py-2 text-slate-400">deleted_at</td>
                          <td className="py-2 text-slate-400">TIMESTAMP</td>
                          <td className="py-2 text-slate-500">Nullable (SoftDelete)</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>

                {/* Audit log table with request_id, event_id, severity */}
                <div className="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-5">
                  <div className="flex items-center justify-between mb-4">
                    <h3 className="text-sm font-bold text-white flex items-center gap-2">
                      <Activity className="w-4 h-4 text-amber-500" />
                      Struktur Tabel `audit_logs`
                    </h3>
                    <span className="text-[10px] bg-amber-500/10 text-amber-300 border border-amber-500/20 px-2 py-0.5 rounded font-mono">Immutable Log</span>
                  </div>
                  <div className="overflow-x-auto">
                    <table className="w-full text-left text-xs text-slate-300">
                      <thead>
                        <tr className="border-b border-slate-800 text-slate-400 text-[10px] uppercase font-mono">
                          <th className="py-2">Kolom</th>
                          <th className="py-2">Tipe</th>
                          <th className="py-2">Fungsi / Deskripsi</th>
                        </tr>
                      </thead>
                      <tbody className="font-mono divide-y divide-slate-800/50">
                        <tr>
                          <td className="py-2 text-amber-400 font-bold">event_id</td>
                          <td className="py-2 text-slate-400">UUID</td>
                          <td className="py-2 text-slate-300">Pengenal unik untuk satu kejadian bisnis (Unique)</td>
                        </tr>
                        <tr>
                          <td className="py-2 text-amber-400 font-bold">request_id</td>
                          <td className="py-2 text-slate-400">UUID</td>
                          <td className="py-2 text-slate-300">Melacak aliran log dari satu HTTP request yang sama</td>
                        </tr>
                        <tr>
                          <td className="py-2 text-amber-400 font-bold">severity</td>
                          <td className="py-2 text-slate-400">ENUM</td>
                          <td className="py-2 text-slate-200">
                            <span className="text-emerald-400">info</span>, <span className="text-amber-400">warning</span>, <span className="text-rose-400">critical</span>
                          </td>
                        </tr>
                        <tr>
                          <td className="py-2 text-slate-200">user_id</td>
                          <td className="py-2 text-slate-400">BIGINT</td>
                          <td className="py-2 text-slate-400">Aktor pengubah (Foreign Key users, Nullable)</td>
                        </tr>
                        <tr>
                          <td className="py-2 text-slate-200">old_values</td>
                          <td className="py-2 text-slate-400">JSON</td>
                          <td className="py-2 text-slate-500">Kondisi data SEBELUM dimodifikasi</td>
                        </tr>
                        <tr>
                          <td className="py-2 text-slate-200">new_values</td>
                          <td className="py-2 text-slate-400">JSON</td>
                          <td className="py-2 text-slate-500">Kondisi data SESUDAH dimodifikasi</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </motion.div>
          )}

          {activeTab === 'ci-cd' && (
            <motion.div 
              initial={{ opacity: 0, y: 10 }}
              animate={{ opacity: 1, y: 0 }}
              className="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-6"
            >
              <h3 className="text-sm font-bold text-white mb-4 flex items-center gap-2">
                <Terminal className="w-4 h-4 text-emerald-400" />
                Alur Kontrol Kualitas Otomatis (GitHub Actions CI)
              </h3>
              <p className="text-xs text-slate-400 leading-relaxed mb-6">
                Untuk menjaga agar kode Monolith tetap murni, aman, dan tidak terdegradasi seiring waktu, alur kerja pemeriksaan otomatis dilakukan di cloud pada setiap Pull Request.
              </p>

              <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div className="bg-slate-950/50 p-4 rounded-xl border border-slate-850 flex flex-col gap-3">
                  <div className="flex items-center gap-2 text-amber-400 font-semibold text-xs">
                    <span className="p-1.5 bg-amber-400/10 rounded-lg text-amber-400">1</span>
                    Laravel Pint (Linter)
                  </div>
                  <p className="text-[11px] text-slate-400 leading-relaxed">
                    Menyaring standardisasi kode gaya penulisan secara otomatis sesuai kaidah preset <strong>Laravel Formatter</strong>.
                  </p>
                </div>

                <div className="bg-slate-950/50 p-4 rounded-xl border border-slate-850 flex flex-col gap-3">
                  <div className="flex items-center gap-2 text-cyan-400 font-semibold text-xs">
                    <span className="p-1.5 bg-cyan-400/10 rounded-lg text-cyan-400">2</span>
                    PHPStan / Larastan (Static Analysis)
                  </div>
                  <p className="text-[11px] text-slate-400 leading-relaxed">
                    Mengecek ketepatan tipe variabel (Type Safety) pada <strong>Level 5</strong> guna mencegah runtime crash di produksi.
                  </p>
                </div>

                <div className="bg-slate-950/50 p-4 rounded-xl border border-slate-850 flex flex-col gap-3">
                  <div className="flex items-center gap-2 text-emerald-400 font-semibold text-xs">
                    <span className="p-1.5 bg-emerald-400/10 rounded-lg text-emerald-400">3</span>
                    Pest Testing Engine
                  </div>
                  <p className="text-[11px] text-slate-400 leading-relaxed">
                    Mengeksekusi pengujian fitur, integrasi, dan <strong>Pest Arch</strong> untuk mengunci batasan Modular Monolith secara ketat.
                  </p>
                </div>
              </div>
            </motion.div>
          )}

          {activeTab === 'rbac' && (
            <motion.div 
              initial={{ opacity: 0, y: 10 }}
              animate={{ opacity: 1, y: 0 }}
              className="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-6"
            >
              <div className="flex items-center justify-between mb-4">
                <h3 className="text-sm font-bold text-white flex items-center gap-2">
                  <Lock className="w-4 h-4 text-indigo-400" />
                  Matriks Hak Akses Terpusat (Spatie RBAC)
                </h3>
                <span className="text-[10px] bg-indigo-500/10 text-indigo-300 border border-indigo-500/20 px-2 py-0.5 rounded font-mono">Role-Based Access</span>
              </div>
              <p className="text-xs text-slate-400 mb-6 leading-relaxed">
                Pengecekan hak akses diletakkan di gerbang awal menggunakan Middleware Route atau di konstruktor layanan menggunakan <code>$this-&gt;middleware('can:permission-name')</code>.
              </p>

              <div className="overflow-x-auto">
                <table className="w-full text-left text-xs text-slate-300">
                  <thead>
                    <tr className="border-b border-slate-850 text-slate-400 font-mono text-[10px] uppercase">
                      <th className="py-3 px-4">Fitur Terlindungi</th>
                      <th className="py-3 px-4 text-center">Super Admin</th>
                      <th className="py-3 px-4 text-center">Bendahara</th>
                      <th className="py-3 px-4 text-center">Kepala Madrasah</th>
                      <th className="py-3 px-4 text-center">Orang Tua</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-slate-850">
                    <tr>
                      <td className="py-3 px-4 font-semibold text-slate-200">Konfigurasi Sistem (`manage-users`)</td>
                      <td className="py-3 px-4 text-center text-emerald-400">Ya</td>
                      <td className="py-3 px-4 text-center text-rose-500">Tidak</td>
                      <td className="py-3 px-4 text-center text-rose-500">Tidak</td>
                      <td className="py-3 px-4 text-center text-rose-500">Tidak</td>
                    </tr>
                    <tr>
                      <td className="py-3 px-4 font-semibold text-slate-200">Manajemen Siswa (`manage-students`)</td>
                      <td className="py-3 px-4 text-center text-emerald-400">Ya</td>
                      <td className="py-3 px-4 text-center text-emerald-400">Ya</td>
                      <td className="py-3 px-4 text-center text-rose-500">Tidak</td>
                      <td className="py-3 px-4 text-center text-rose-500">Tidak</td>
                    </tr>
                    <tr>
                      <td className="py-3 px-4 font-semibold text-slate-200">Laporan Keuangan (`view-reports`)</td>
                      <td className="py-3 px-4 text-center text-emerald-400">Ya</td>
                      <td className="py-3 px-4 text-center text-emerald-400">Ya</td>
                      <td className="py-3 px-4 text-center text-emerald-400">Ya (Read-only)</td>
                      <td className="py-3 px-4 text-center text-rose-500">Tidak</td>
                    </tr>
                    <tr>
                      <td className="py-3 px-4 font-semibold text-slate-200">Persetujuan Pembayaran (`approve-payments`)</td>
                      <td className="py-3 px-4 text-center text-emerald-400">Ya</td>
                      <td className="py-3 px-4 text-center text-emerald-400">Ya</td>
                      <td className="py-3 px-4 text-center text-rose-500">Tidak</td>
                      <td className="py-3 px-4 text-center text-rose-500">Tidak</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </motion.div>
          )}
        </main>
      </div>

      {/* Footer System Stats */}
      <footer className="border-t border-slate-900 bg-slate-950 px-6 py-3 flex items-center justify-between text-slate-500 text-[10px]" id="footer_section">
        <div>Sistem Informasi Administrasi Madrasah (SIAM) • Versi v1.0.0-dev</div>
        <div className="flex items-center gap-1.5">
          <span className="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
          Workspace Sinkron & Terintegrasi
        </div>
      </footer>
    </div>
  );
}
