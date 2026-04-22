import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import SalesPagePreview from '@/Components/SalesPages/SalesPagePreview';
import { Head, Link, router } from '@inertiajs/react';

export default function Show({ salesPage }) {
    const regenerate = () => router.post(route('sales-pages.regenerate', salesPage.id));

    const remove = () => {
        if (!window.confirm('Delete this sales page?')) {
            return;
        }

        router.delete(route('sales-pages.destroy', salesPage.id));
    };

    return (
        <AuthenticatedLayout
            header={<h2 className="text-xl font-semibold leading-tight text-gray-800">Preview Sales Page</h2>}
        >
            <Head title={salesPage.product_name} />

            <div className="mx-auto max-w-7xl space-y-4 px-4 py-8 sm:px-6 lg:px-8">
                <div className="flex flex-wrap gap-2">
                    <Link href={route('sales-pages.index')} className="rounded-md border border-gray-300 px-4 py-2 text-sm">
                        Back
                    </Link>
                    <button type="button" className="rounded-md bg-indigo-600 px-4 py-2 text-sm text-white" onClick={regenerate}>
                        Re-generate
                    </button>
                    <button type="button" className="rounded-md bg-red-600 px-4 py-2 text-sm text-white" onClick={remove}>
                        Delete
                    </button>
                </div>

                <SalesPagePreview salesPage={salesPage} />
            </div>
        </AuthenticatedLayout>
    );
}
