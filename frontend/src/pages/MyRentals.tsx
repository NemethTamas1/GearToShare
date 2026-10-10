import MyRentalsList from '../components/profile/MyRentalsList';

export default function MyRentals() {
  return (
    <div className="h-full overflow-y-auto bg-bg font-sans">
      <h1 className="px-5 pt-6 pb-2 text-lg font-bold text-ink">Bérléseim</h1>
      <MyRentalsList />
    </div>
  );
}