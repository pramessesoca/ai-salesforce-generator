import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import SalesPagePreview from '@/Components/SalesPages/SalesPagePreview';
import { Head, useForm, usePage } from '@inertiajs/react';

export default function Dashboard({ previewPage }) {
    const { errors } = usePage().props;
    const { data, setData, post, processing, reset } = useForm({
        product_name: '',
        description: '',
        key_features: [''],
        target_audience: '',
        price: '',
        unique_selling_points: [''],
    });

    const updateArrayValue = (field, index, value) => {
        const next = [...data[field]];
        next[index] = value;
        setData(field, next);
    };

    const addArrayItem = (field) => setData(field, [...data[field], '']);

    const removeArrayItem = (field, index) => {
        if (data[field].length === 1) {
            return;
        }

        setData(field, data[field].filter((_, itemIndex) => itemIndex !== index));
    };

    const submit = (e) => {
        e.preventDefault();

        post(route('sales-pages.generate'), {
            onSuccess: (page) => {
                console.log('Generate response:', page.props);
                reset('description', 'target_audience', 'price');
            },
            onError: (formErrors) => {
                console.log('Generate error response:', formErrors);
            },
        });
    };

    return (
        <AuthenticatedLayout
            header={<h2 className="text-xl font-semibold leading-tight text-gray-800">Sales Page Generator</h2>}
        >
            <Head title="Sales Page Generator" />

            <div className="mx-auto grid w-full max-w-7xl gap-6 px-4 py-8 sm:px-6 lg:grid-cols-2 lg:px-8">
                <form onSubmit={submit} className="space-y-5 rounded-2xl bg-white p-6 shadow-sm">
                    <div>
                        <InputLabel htmlFor="product_name" value="Product/Service Name" />
                        <TextInput
                            id="product_name"
                            className="mt-1 block w-full"
                            value={data.product_name}
                            onChange={(e) => setData('product_name', e.target.value)}
                        />
                        <InputError className="mt-2" message={errors.product_name} />
                    </div>

                    <div>
                        <InputLabel htmlFor="description" value="Description" />
                        <textarea
                            id="description"
                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            rows={4}
                            value={data.description}
                            onChange={(e) => setData('description', e.target.value)}
                        />
                        <InputError className="mt-2" message={errors.description} />
                    </div>

                    <MultiInput
                        label="Key Features"
                        values={data.key_features}
                        onAdd={() => addArrayItem('key_features')}
                        onRemove={(index) => removeArrayItem('key_features', index)}
                        onChange={(index, value) => updateArrayValue('key_features', index, value)}
                    />
                    <InputError className="mt-2" message={errors.key_features} />

                    <div>
                        <InputLabel htmlFor="target_audience" value="Target Audience" />
                        <TextInput
                            id="target_audience"
                            className="mt-1 block w-full"
                            value={data.target_audience}
                            onChange={(e) => setData('target_audience', e.target.value)}
                        />
                        <InputError className="mt-2" message={errors.target_audience} />
                    </div>

                    <div>
                        <InputLabel htmlFor="price" value="Price" />
                        <TextInput
                            id="price"
                            className="mt-1 block w-full"
                            value={data.price}
                            onChange={(e) => setData('price', e.target.value)}
                        />
                        <InputError className="mt-2" message={errors.price} />
                    </div>

                    <MultiInput
                        label="Unique Selling Points"
                        values={data.unique_selling_points}
                        onAdd={() => addArrayItem('unique_selling_points')}
                        onRemove={(index) => removeArrayItem('unique_selling_points', index)}
                        onChange={(index, value) => updateArrayValue('unique_selling_points', index, value)}
                    />
                    <InputError className="mt-2" message={errors.unique_selling_points} />
                    <InputError className="mt-2" message={errors.generation} />

                    <PrimaryButton disabled={processing}>{processing ? 'Generating...' : 'Generate Sales Page'}</PrimaryButton>
                </form>

                <div className="space-y-4">
                    <h3 className="text-lg font-semibold text-gray-900">Live Preview</h3>
                    <SalesPagePreview salesPage={previewPage} />
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

function MultiInput({ label, values, onAdd, onRemove, onChange }) {
    return (
        <div>
            <div className="mb-2 flex items-center justify-between">
                <InputLabel value={label} />
                <button type="button" className="text-sm font-medium text-indigo-600" onClick={onAdd}>
                    + Add
                </button>
            </div>
            <div className="space-y-2">
                {values.map((value, index) => (
                    <div key={`${label}-${index}`} className="flex gap-2">
                        <TextInput className="block w-full" value={value} onChange={(e) => onChange(index, e.target.value)} />
                        <button
                            type="button"
                            className="rounded-md border border-gray-300 px-3 text-sm text-gray-600"
                            onClick={() => onRemove(index)}
                        >
                            Remove
                        </button>
                    </div>
                ))}
            </div>
        </div>
    );
}
