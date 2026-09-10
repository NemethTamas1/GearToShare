import { useAuth } from '../context/AuthContext';

export default function Profile() {
  const { user, logout } = useAuth();

  if (!user) return <p>Nincs bejelentkezve.</p>;

  return (
    <div>
      <p>Bejelentkezve mint: {user.name} ({user.email})</p>
      <button onClick={logout}>Kijelentkezés</button>
    </div>
  );
}