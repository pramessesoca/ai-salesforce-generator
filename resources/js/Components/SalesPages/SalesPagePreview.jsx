export default function SalesPagePreview({ salesPage }) {
    if (!salesPage) {
        return (
            <div className="rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center text-gray-500">
                Your generated sales page will appear here after you click Generate.
            </div>
        );
    }

    return (
        <div className="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <section className="bg-gradient-to-r from-indigo-700 to-sky-600 p-8 text-white sm:p-12">
                <p className="text-sm font-semibold uppercase tracking-wide text-indigo-100">
                    {salesPage.product_name}
                </p>
                <h1 className="mt-3 text-3xl font-bold leading-tight sm:text-4xl">
                    {salesPage.headline}
                </h1>
                <p className="mt-3 text-lg text-indigo-100">{salesPage.subheadline}</p>
            </section>

            <section className="space-y-8 p-8 sm:p-10">
                <div>
                    <h2 className="text-xl font-semibold text-gray-900">About the Product</h2>
                    <p className="mt-2 text-gray-700">{salesPage.product_description}</p>
                </div>

                <div className="grid gap-8 md:grid-cols-2">
                    <div>
                        <h3 className="text-lg font-semibold text-gray-900">Key Benefits</h3>
                        <ul className="mt-3 space-y-2 text-gray-700">
                            {(salesPage.benefits || []).map((benefit, index) => (
                                <li key={`${benefit}-${index}`} className="rounded-lg bg-gray-50 p-3">
                                    {benefit}
                                </li>
                            ))}
                        </ul>
                    </div>
                    <div>
                        <h3 className="text-lg font-semibold text-gray-900">Features</h3>
                        <ul className="mt-3 space-y-2 text-gray-700">
                            {(salesPage.features_breakdown || []).map((feature, index) => (
                                <li key={`${feature}-${index}`} className="rounded-lg bg-gray-50 p-3">
                                    {feature}
                                </li>
                            ))}
                        </ul>
                    </div>
                </div>

                <div className="rounded-xl border border-slate-200 bg-slate-50 p-5">
                    <h3 className="text-lg font-semibold text-gray-900">Social Proof</h3>
                    <p className="mt-2 text-gray-700">{salesPage.social_proof_placeholder}</p>
                </div>

                <div className="rounded-xl border border-indigo-100 bg-indigo-50 p-6">
                    <p className="text-sm uppercase tracking-wide text-indigo-700">Pricing</p>
                    <p className="mt-1 text-2xl font-bold text-indigo-900">{salesPage.pricing_display}</p>
                    <button
                        type="button"
                        className="mt-4 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white"
                    >
                        {salesPage.cta_text}
                    </button>
                    <p className="mt-2 text-sm text-indigo-700">{salesPage.cta_subtext}</p>
                </div>
            </section>
        </div>
    );
}
