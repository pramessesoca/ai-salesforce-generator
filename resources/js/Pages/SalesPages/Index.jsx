import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';

export default function Index({ salesPages }) {
    const deletePage = (id) => {
        if (!window.confirm('Delete this sales page?')) {
            return;
        }

        router.delete(route('sales-pages.destroy', id));
    };

    return (
        <AuthenticatedLayout header={<h2 className="text-xl font-semibold leading-tight text-gray-800">Saved Pages</h2>}>
            <Head title="Saved Pages" />

            <div className="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <div className="overflow-hidden rounded-xl bg-white shadow-sm">
                    <table className="min-w-full divide-y divide-gray-200">
                        <thead className="bg-gray-50">
                            <tr>
                                <th className="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Product</th>
                                <th className="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Headline</th>
                                <th className="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Updated</th>
                                <th className="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-600">Action</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-100">
                            {salesPages.map((page) => (
                                <tr key={page.id}>
                                    <td className="px-4 py-3 text-sm text-gray-900">{page.product_name}</td>
                                    <td className="px-4 py-3 text-sm text-gray-700">{page.headline}</td>
                                    <td className="px-4 py-3 text-sm text-gray-500">{page.updated_at}</td>
                                    <td className="px-4 py-3 text-right text-sm">
                                        <Link className="mr-4 font-medium text-indigo-600" href={route('sales-pages.show', page.id)}>
                                            View
                                        </Link>
                                        <button
                                            type="button"
                                            className="font-medium text-red-600"
                                            onClick={() => deletePage(page.id)}
                                        >
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            ))}
                            {salesPages.length === 0 && (
                                <tr>
                                    <td colSpan={4} className="px-4 py-6 text-center text-sm text-gray-500">
                                        No saved pages yet.
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
