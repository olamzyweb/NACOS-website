import { useState, useEffect } from "react";
import Layout from "@/components/Layout";
import { ArrowRight, Crown, BarChart2, Vote } from "lucide-react";
import { motion, AnimatePresence } from "framer-motion";
import { Link, useSearchParams } from "react-router-dom";
import { fetchVotingApi, resolveImageUrl } from "@/lib/api";
import { useToast } from "@/hooks/use-toast";

const GROUP_FILTERS = [
  { label: "All",          value: "all" },
  { label: "Tech",         value: "tech",     cats: ["Best Programmer of the Year","Most Innovative Student","Tech Influencer of the Year","Best Tech Content Creator","AI/Tech Enthusiast Award","Most Creative Developer","Best Creative Designer"] },
  { label: "Leadership",   value: "leader",   cats: ["HOC of the Year","Assistant HOC of the Year","Most Outstanding Leader","Best Executive of the Year","Best Team Player"] },
  { label: "Social",       value: "social",   cats: ["Social Influencer of the Year","Social Personality of the Year","Most Popular Student","Mr. Money of the Year","Fashion Icon of the Department"] },
  { label: "Creative",     value: "creative", cats: ["Artist of the Year","Content Creator of the Year","CEO of the Year","Tech Entrepreneur/Student Founder","Best Brand of the Year"] },
  { label: "Sports",       value: "sports",   cats: ["Best Male Footballer of the Year","Best Female Footballer of the Year","Best Football Team of the Year"] },
];

const BarRow = ({
  nominee,
  rank,
  maxVotes,
  delay = 0,
}: {
  nominee: any;
  rank: number;
  maxVotes: number;
  delay?: number;
}) => {
  const pct = maxVotes > 0 ? Math.max(10, (nominee.voteCount / maxVotes) * 100) : 10;
  const rankColors = [
    "from-amber-400 to-yellow-300",
    "from-slate-400 to-slate-300",
    "from-orange-500 to-amber-400",
  ];
  const barColor = rankColors[rank - 1] ?? "from-primary to-blue-400";
  const badgeColors = [
    "bg-amber-400 text-white",
    "bg-slate-300 text-white",
    "bg-orange-400 text-white",
  ];
  const badgeColor = badgeColors[rank - 1] ?? "bg-slate-100 text-slate-500";
  const rankLabel = rank === 1 ? "Rank 1" : rank === 2 ? "Rank 2" : rank === 3 ? "Rank 3" : `Rank ${rank}`;

  return (
    <motion.div
      initial={{ opacity: 0, x: -30 }}
      animate={{ opacity: 1, x: 0 }}
      transition={{ delay, duration: 0.4 }}
      className="flex items-center gap-4 group"
    >
      <div className={`h-9 w-9 shrink-0 rounded-xl flex items-center justify-center text-[10px] font-black shadow-sm ${badgeColor}`}>
        {rankLabel}
      </div>

      <div className="flex-1 relative h-14 bg-slate-100 rounded-full overflow-hidden">
        <motion.div
          initial={{ width: 0 }}
          animate={{ width: `${pct}%` }}
          transition={{ duration: 1.4, delay: delay + 0.1, ease: [0.34, 1.2, 0.64, 1] }}
          className={`absolute inset-y-0 left-0 rounded-full bg-gradient-to-r ${barColor} flex items-center min-w-[3rem]`}
        >
          <span className="pl-5 text-xs font-black text-white drop-shadow truncate max-w-[55%] leading-none pr-10 whitespace-nowrap">
            {nominee.name}
          </span>
        </motion.div>

        <motion.div
          initial={{ left: "-28px" }}
          animate={{ left: `calc(${pct}% - 28px)` }}
          transition={{ duration: 1.4, delay: delay + 0.1, ease: [0.34, 1.2, 0.64, 1] }}
          className="absolute top-1 bottom-1 w-12 rounded-full overflow-hidden border-[3px] border-white shadow-xl z-10 bg-slate-200"
        >
          <img
            src={resolveImageUrl(nominee.photo)}
            alt={nominee.name}
            className="h-full w-full object-cover"
            onError={(e) => {
              (e.target as HTMLImageElement).src = `https://ui-avatars.com/api/?name=${encodeURIComponent(nominee.name)}&background=0ea5e9&color=fff&size=96&bold=true&font-size=0.4`;
            }}
          />
        </motion.div>
      </div>

      <Link
        to={`/voting/${nominee.categorySlug || 'category'}/${nominee.slug || nominee.id}`}
        onClick={(e) => e.stopPropagation()}
        className="shrink-0 hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-slate-200 bg-white text-xs font-bold text-slate-500 hover:bg-primary hover:text-white hover:border-primary transition-all shadow-sm whitespace-nowrap"
      >
        <Vote className="h-3 w-3" />
        Vote
      </Link>
    </motion.div>
  );
};

const Leaderboard = () => {
  const [activeFilter, setActiveFilter] = useState("all");
  const [nominees, setNominees] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const { toast } = useToast();
  const [searchParams, setSearchParams] = useSearchParams();

  useEffect(() => {
    const reference = searchParams.get("reference");
    if (!reference) return;

    toast({
      title: "Verifying Votes",
      description: "Please wait while we confirm your payment...",
    });

    fetchVotingApi(`/transactions/verify/${reference}`)
      .then((res) => {
        const verificationStatus = res?.data?.status || res?.status;
        if (verificationStatus === "success") {
          toast({
            title: "Payment Confirmed!",
            description: "Your votes have been counted. Thank you for your support!",
          });
          // Refresh leaderboard
          fetchVotingApi("/leaderboard")
            .then((data) => {
              setNominees(data.nominees || []);
            });
        } else {
          toast({
            title: "Verification Pending",
            description: "Your transaction status is: " + (verificationStatus || "unknown"),
            variant: "destructive",
          });
        }
      })
      .catch((err) => {
        console.error("Verification failed:", err);
        toast({
          title: "Verification Error",
          description: "Could not confirm payment automatically. If you were debited, your votes will be tallied shortly.",
          variant: "destructive",
        });
      })
      .finally(() => {
        searchParams.delete("reference");
        setSearchParams(searchParams);
      });
  }, [searchParams]);

  useEffect(() => {
    fetchVotingApi("/leaderboard")
      .then((data) => {
        setNominees(data.nominees || []);
      })
      .catch((err) => {
        console.error("Failed to fetch leaderboard standings:", err);
      })
      .finally(() => {
        setLoading(false);
      });
  }, []);

  const filtered = activeFilter === "all"
    ? nominees
    : nominees.filter((n) => {
        const grp = GROUP_FILTERS.find((g) => g.value === activeFilter);
        return grp?.cats?.includes(n.categoryName);
      });

  const maxVotes = filtered[0]?.voteCount || 1;

  if (loading) {
    return (
      <Layout>
        <div className="min-h-screen bg-slate-50 flex items-center justify-center">
          <p className="text-slate-400 font-bold text-lg">Loading leaderboard standings...</p>
        </div>
      </Layout>
    );
  }

  return (
    <Layout>
      <div className="bg-slate-50 min-h-screen pb-24">

        <div className="bg-[#08111d] pt-32 pb-24 relative overflow-hidden">
          <div className="absolute inset-0 opacity-15">
            <div className="absolute top-0 right-0 w-[600px] h-[600px] bg-primary rounded-full blur-[130px]" />
            <div className="absolute bottom-0 left-0 w-[600px] h-[600px] bg-blue-500 rounded-full blur-[130px]" />
          </div>

          <div className="container px-4 relative z-10">
            <Link
              to="/awards"
              className="inline-flex items-center gap-2 mb-6 px-5 py-2.5 rounded-full bg-white text-slate-900 font-black text-sm shadow-lg hover:bg-primary hover:text-white transition-all hover:-translate-x-1"
            >
              <ArrowRight className="h-4 w-4 rotate-180" />
              Back to Awards
            </Link>

            <div className="flex items-start gap-6">
              <div className="h-16 w-16 rounded-2xl bg-amber-400 flex items-center justify-center shadow-2xl shrink-0">
                <Crown className="h-8 w-8 text-white" />
              </div>
              <div>
                <h1 className="text-4xl sm:text-6xl font-black text-white uppercase tracking-tight">
                  Real‑Time <span className="text-primary">Leaderboard</span>
                </h1>
                <p className="text-white/50 mt-3 text-lg max-w-2xl">
                  Live standings across all award categories — watch who is pulling ahead.
                </p>
              </div>
            </div>
          </div>
        </div>

        <div className="container px-4 mt-[-3rem] relative z-10">
          <div className="bg-white rounded-[2.5rem] border border-slate-200 shadow-2xl overflow-hidden">

            <div className="p-6 sm:p-8 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5">
              <div className="flex items-center gap-3">
                <div className="h-10 w-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                  <BarChart2 className="h-5 w-5" />
                </div>
                <div>
                  <h2 className="text-xl font-black text-slate-900">Global Standings</h2>
                  <p className="text-xs text-slate-400 font-medium">{filtered.length} nominees · position only, no vote counts</p>
                </div>
              </div>

              <div className="flex flex-wrap gap-2">
                {GROUP_FILTERS.map((f) => (
                  <button
                    key={f.value}
                    onClick={() => setActiveFilter(f.value)}
                    className={`px-4 py-2 rounded-full text-xs font-black transition-all ${
                      activeFilter === f.value
                        ? "bg-primary text-white shadow-md shadow-primary/30"
                        : "bg-slate-100 text-slate-500 hover:bg-slate-200"
                    }`}
                  >
                    {f.label}
                  </button>
                ))}
              </div>
            </div>

            <div className="p-6 sm:p-8 space-y-5">
              <AnimatePresence mode="wait">
                <motion.div
                  key={activeFilter}
                  initial={{ opacity: 0 }}
                  animate={{ opacity: 1 }}
                  exit={{ opacity: 0 }}
                  className="space-y-4"
                >
                  {filtered.map((nominee, i) => (
                    <BarRow
                      key={nominee.id}
                      nominee={nominee}
                      rank={i + 1}
                      maxVotes={maxVotes}
                      delay={i * 0.05}
                    />
                  ))}

                  {filtered.length === 0 && (
                    <div className="py-20 text-center text-slate-400 font-bold">
                      No nominees in this group yet.
                    </div>
                  )}
                </motion.div>
              </AnimatePresence>
            </div>

            <div className="p-6 border-t border-slate-100 bg-slate-50 text-center">
              <p className="text-slate-400 text-sm font-medium">
                Positions update live as votes are cast · <span className="font-bold text-slate-600">N100 per vote</span> · Secured by Korapay
              </p>
            </div>
          </div>
        </div>
      </div>
    </Layout>
  );
};

export default Leaderboard;
