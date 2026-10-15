export default function App() {
  return (
    <div className="min-h-screen bg-gray-900 flex items-center justify-center p-6">
      <div className="text-center max-w-2xl">
        <div className="text-6xl mb-6">🏠</div>
        <h1 className="text-4xl font-bold text-white mb-4">
          CRM — База недвижимости
        </h1>
        <p className="text-blue-200 text-lg mb-8">
          Backend API на PHP / Slim Framework
        </p>
        <div className="bg-white/10 backdrop-blur-sm rounded-xl p-6 border border-white/20 text-left">
          <h2 className="text-white font-semibold mb-3">📂 Справочники географии:</h2>
          <ul className="text-blue-100 text-sm space-y-1">
            <li>• <code className="bg-white/20 px-1.5 py-0.5 rounded">src/Geography/MacroRegion/</code> — Макрорегионы</li>
            <li>• <code className="bg-white/20 px-1.5 py-0.5 rounded">src/Geography/Region/</code> — Регионы</li>
            <li>• <code className="bg-white/20 px-1.5 py-0.5 rounded">src/Geography/District/</code> — Районы</li>
            <li>• <code className="bg-white/20 px-1.5 py-0.5 rounded">src/Geography/Point/</code> — Населённые пункты</li>
          </ul>
          <h2 className="text-white font-semibold mt-4 mb-3">📄 API документация:</h2>
          <p className="text-blue-100 text-sm">
            <code className="bg-white/20 px-1.5 py-0.5 rounded">public/documentation/api/geography.yaml</code>
          </p>
        </div>
      </div>
    </div>
  );
}
