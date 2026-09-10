import { useState, type FormEvent } from 'react';
import { useAuth } from '../context/AuthContext';
import type { LoginPayload } from '../types/auth';
import axios from 'axios';

export default function Login() {
  const { login } = useAuth();
  const [form, setForm] = useState<LoginPayload>({ email: '', password: '' });
  const [error, setError] = useState<string | null>(null);

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();
    setError(null);
    try {
      await login(form);
    } catch (err) {
      if (axios.isAxiosError(err)) {
        setError(err.response?.data?.message ?? 'Hiba történt.');
      }
    }
  };

  return (
    <form onSubmit={handleSubmit}>
      <input placeholder="Email" value={form.email} onChange={(e) => setForm({ ...form, email: e.target.value })} />
      <input type="password" placeholder="Jelszó" value={form.password} onChange={(e) => setForm({ ...form, password: e.target.value })} />
      <button type="submit">Bejelentkezés</button>
      {error && <p style={{ color: 'red' }}>{error}</p>}
    </form>
  );
}