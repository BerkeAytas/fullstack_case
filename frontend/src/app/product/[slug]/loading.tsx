export default function Loading() {
  return (
    <div className="max-w-2xl mx-auto p-8 animate-pulse">
      <div className="h-8 bg-gray-200 rounded w-1/2" />
      <div className="h-4 bg-gray-200 rounded w-3/4 mt-3" />
      <div className="h-6 bg-gray-200 rounded w-1/4 mt-4" />
      <div className="h-20 bg-gray-200 rounded mt-6" />
    </div>
  );
}
