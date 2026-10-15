export default function App() {
  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 flex items-center justify-center p-6">
      <div className="text-center max-w-2xl">
        <div className="text-6xl mb-6">🏠</div>
        <h1 className="text-4xl font-bold text-white mb-4">
          CRM для базы недвижимости
        </h1>
        <p className="text-blue-200 text-lg mb-8">
          Система управления объектами, клиентами и сделками
        </p>
        <div className="bg-white/10 backdrop-blur-sm rounded-xl p-6 border border-white/20">
          <p className="text-blue-100 text-sm">
            🚧 Проект в разработке. Смотрите <code className="bg-white/20 px-2 py-0.5 rounded">README.md</code> для подробностей.
          </p>
        </div>
        <div className="mt-8 grid grid-cols-3 gap-4">
          <div className="bg-white/5 rounded-lg p-4 border border-white/10">
            <div className="text-2xl mb-1">📦</div>
            <div className="text-white text-sm font-medium">Объекты</div>
          </div>
          <div className="bg-white/5 rounded-lg p-4 border border-white/10">
            <div className="text-2xl mb-1">👥</div>
            <div className="text-white text-sm font-medium">Клиенты</div>
          </div>
          <div className="bg-white/5 rounded-lg p-4 border border-white/10">
            <div className="text-2xl mb-1">🤝</div>
            <div className="text-white text-sm font-medium">Сделки</div>
          </div>
        </div>
      </div>
    </div>
  );
}
